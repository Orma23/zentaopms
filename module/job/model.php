<?php
/**
 * The model file of job module of ZenTaoCMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     job
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class jobModel extends model
{
    /**
     * Get by id.
     *
     * @param  int    $id
     * @access public
     * @return object
     */
    public function getByID($id)
    {
        $job = $this->dao->select('*')->from(TABLE_JOB)->where('id')->eq($id)->fetch();
        if(strtolower($job->engine) == 'gitlab')
        {
            $pipeline = json_decode($job->pipeline);
            if(!isset($pipeline->reference)) return $job;
            $job->project   = $pipeline->project;
            $job->reference = $pipeline->reference;
        }
        return $job;
    }

    /**
     * Get job list.
     *
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getList($orderBy = 'id_desc', $pager = null)
    {
        return $this->dao->select('t1.*, t2.name as repoName, t3.name as jenkinsName')->from(TABLE_JOB)->alias('t1')
            ->leftJoin(TABLE_REPO)->alias('t2')->on('t1.repo=t2.id')
            ->leftJoin(TABLE_PIPELINE)->alias('t3')->on('t1.server=t3.id')
            ->where('t1.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get list by triggerType field.
     *
     * @param  string  $triggerType
     * @param  array   $repoIdList
     * @access public
     * @return array
     */
    public function getListByTriggerType($triggerType, $repoIdList = array())
    {
        return $this->dao->select('*')->from(TABLE_JOB)
            ->where('deleted')->eq('0')
            ->andWhere('triggerType')->eq($triggerType)
            ->beginIF($repoIdList)->andWhere('repo')->in($repoIdList)->fi()
            ->fetchAll('id');
    }

    /**
     * Get trigger config.
     *
     * @param  object $job
     * @access public
     * @return string
     */
    public function getTriggerConfig($job)
    {
          $triggerType = zget($this->lang->job->triggerTypeList, $job->triggerType);
          if($job->triggerType == 'tag')
          {
              if(empty($job->svnDir)) return $triggerType;

              $triggerType = $this->lang->job->dirChange;
              return "{$triggerType}({$job->svnDir})";
          }

          if($job->triggerType == 'commit') return "{$triggerType}({$job->comment})";

          if($job->triggerType == 'schedule')
          {
              $atDay = '';
              foreach(explode(',', $job->atDay) as $day) $atDay .= zget($this->lang->datepicker->dayNames, trim($day), '') . ',';
              $atDay = trim($atDay, ',');
              return "{$triggerType}({$atDay}, {$job->atTime})";
          }
    }

    /**
     * Get trigger group.
     *
     * @param  string $triggerType
     * @param  array  $repoIdList
     * @access public
     * @return array
     */
    public function getTriggerGroup($triggerType, $repoIdList)
    {
        $jobs  = $this->getListByTriggerType($triggerType, $repoIdList);
        $group = array();
        foreach($jobs as $job) $group[$job->repo][$job->id] = $job;

        return $group;
    }

    /**
     * Create a job.
     *
     * @access public
     * @return int|bool
     */
    public function create()
    {
        $job = fixer::input('post')
            ->setDefault('atDay', '')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->remove('repoType,reference')
            ->get();

        if($job->engine == 'jenkins')
        {
            $job->server   = (int)zget($job, 'jkServer', 0);
            $job->pipeline = zget($job, 'jkTask', '');
        }

        if(strtolower($job->engine) == 'gitlab')
        {
            $repo    = $this->loadModel('repo')->getRepoByID($job->gitlabRepo);
            $project = zget($repo, 'project');

            $job->repo     = $job->gitlabRepo;
            $job->server   = (int)zget($repo, 'gitlab', 0);
            $job->pipeline = json_encode(array('project' => $project, 'reference' => $this->post->reference));
        }

        unset($job->jkServer);
        unset($job->jkTask);
        unset($job->gitlabRepo);

        if($job->triggerType == 'schedule') $job->atDay = empty($_POST['atDay']) ? '' : join(',', $this->post->atDay);

        $job->svnDir = '';
        if($job->triggerType == 'tag' and $this->post->repoType == 'Subversion')
        {
            $job->svnDir = array_pop($_POST['svnDir']);
            if($job->svnDir == '/' and $_POST['svnDir']) $job->svnDir = array_pop($_POST['svnDir']);
        }

        $customParam = array();
        foreach($job->paramName as $key => $paramName)
        {
            $paramValue = zget($job->paramValue, $key, '');

            if(empty($paramName) and !empty($paramValue))
            {
                dao::$errors[] = $this->lang->job->inputName;
                return false;
            }

            if(!empty($paramName) and !validater::checkREG($paramName, '/^[A-Za-z_0-9]+$/'))
            {
                dao::$errors[] = $this->lang->job->invalidName;
                return false;
            }

            if(!empty($paramName)) $customParam[$paramName] = $paramValue;
        }
        unset($job->paramName);
        unset($job->paramValue);
        unset($job->custom);
        $job->customParam = json_encode($customParam);

        $this->dao->insert(TABLE_JOB)->data($job)
            ->batchCheck($this->config->job->create->requiredFields, 'notempty')

            ->batchCheckIF($job->triggerType === 'schedule', "atDay,atTime", 'notempty')
            ->batchCheckIF($job->triggerType === 'commit', "comment", 'notempty')
            ->batchCheckIF(($this->post->repoType == 'Subversion' and $job->triggerType == 'tag'), "svnDir", 'notempty')
            ->autoCheck()
            ->exec();
        if(dao::isError()) return false;

        $id = $this->dao->lastInsertId();
        if(strtolower($job->engine) == 'jenkins') $this->initJob($id, $job, $this->post->repoType);
        return $id;
    }

    /**
     * Update a job.
     *
     * @param  int    $id
     * @access public
     * @return bool
     */
    public function update($id)
    {
        $job = fixer::input('post')
            ->setDefault('atDay', '')
            ->setIF($this->post->triggerType != 'commit', 'comment', '')
            ->setIF($this->post->triggerType != 'schedule', 'atDay', '')
            ->setIF($this->post->triggerType != 'schedule', 'atTime', '')
            ->setIF($this->post->triggerType != 'tag', 'lastTag', '')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->remove('repoType,reference')
            ->get();

        if($job->engine == 'jenkins')
        {
            $job->server   = (int)zget($job, 'jkServer', 0);
            $job->pipeline = zget($job, 'jkTask', '');
        }

        if(strtolower($job->engine) == 'gitlab')
        {
            $repo    = $this->loadModel('repo')->getRepoByID($job->gitlabRepo);
            $project = zget($repo, 'project');

            $job->repo     = $job->gitlabRepo;
            $job->server   = (int)zget($repo, 'gitlab', 0);
            $job->pipeline = json_encode(array('project' => $project, 'reference' => $this->post->reference));
        }

        unset($job->jkServer);
        unset($job->jkTask);
        unset($job->gitlabRepo);

        if($job->triggerType == 'schedule') $job->atDay = empty($_POST['atDay']) ? '' : join(',', $this->post->atDay);

        $job->svnDir = '';
        if($job->triggerType == 'tag' and $this->post->repoType == 'Subversion')
        {
            $job->svnDir = array_pop($_POST['svnDir']);
            if($job->svnDir == '/' and $_POST['svnDir']) $job->svnDir = array_pop($_POST['svnDir']);
        }

        $customParam = array();
        foreach($job->paramName as $key => $paramName)
        {
            $paramValue = zget($job->paramValue, $key, '');

            if(empty($paramName) and !empty($paramValue))
            {
                dao::$errors[] = $this->lang->job->inputName;
                return false;
            }

            if(!empty($paramName) and !validater::checkREG($paramName, '/^[A-Za-z_0-9]+$/'))
            {
                dao::$errors[] = $this->lang->job->invalidName;
                return false;
            }

            if(!empty($paramName)) $customParam[$paramName] = $paramValue;
        }

        unset($job->paramName);
        unset($job->paramValue);
        unset($job->custom);
        $job->customParam = json_encode($customParam);

        $this->dao->update(TABLE_JOB)->data($job)
            ->batchCheck($this->config->job->edit->requiredFields, 'notempty')

            ->batchCheckIF($job->triggerType === 'schedule', "atDay,atTime", 'notempty')
            ->batchCheckIF($job->triggerType === 'commit', "comment", 'notempty')
            ->batchCheckIF(($this->post->repoType == 'Subversion' and $job->triggerType == 'tag'), "svnDir", 'notempty')

            ->autoCheck()
            ->where('id')->eq($id)
            ->exec();
        if(dao::isError()) return false;

        $this->initJob($id, $job, $this->post->repoType);
        return true;
    }

    /**
     * Init when create or update job.
     *
     * @param  int    $id
     * @param  object $job
     * @param  string $repoType
     * @access public
     * @return bool
     */
    public function initJob($id, $job, $repoType)
    {
        if(empty($id)) return false;
        if($job->triggerType == 'schedule' and strpos($job->atDay, date('w')) !== false)
        {
            $compiles = $this->dao->select('*')->from(TABLE_COMPILE)->where('job')->eq($id)->andWhere('LEFT(createdDate, 10)')->eq(date('Y-m-d'))->fetchAll();
            foreach($compiles as $compile)
            {
                if(!empty($compile->status)) continue;
                $this->dao->delete()->from(TABLE_COMPILE)->where('id')->eq($compile->id)->exec();
            }
            $this->loadModel('compile')->createByJob($id, $job->atTime, 'atTime');
        }

        if($job->triggerType == 'tag')
        {
            $repo    = $this->loadModel('repo')->getRepoByID($job->repo);
            $lastTag = '';
            if($repoType == 'Subversion')
            {
                $dirs = $this->loadModel('svn')->getRepoTags($repo, $job->svnDir);
                end($dirs);
                $lastTag = current($dirs);
            }
            else
            {
                $tags = $this->loadModel('git')->getRepoTags($repo);
                end($tags);
                $lastTag = current($tags);
            }
            $this->dao->update(TABLE_JOB)->set('lastTag')->eq($lastTag)->where('id')->eq($id)->exec();
        }

        return true;
    }

    /**
     * Exec job.
     *
     * @param  int    $id
     * @param  object $reference
     * @access public
     * @return string|bool
     */
    public function exec($id)
    {
        $job = $this->dao->select('t1.id,t1.name,t1.product,t1.repo,t1.server,t1.pipeline,t1.triggerType,t1.atTime,t1.customParam,t1.engine,t2.name as jenkinsName,t2.url,t2.account,t2.token,t2.password')
            ->from(TABLE_JOB)->alias('t1')
            ->leftJoin(TABLE_PIPELINE)->alias('t2')->on('t1.server=t2.id')
            ->where('t1.id')->eq($id)
            ->fetch();

        if(!$job) return false;

        $repo = $this->loadModel('repo')->getRepoById($job->repo);
        $now  = helper::now();

        /* Save compile data. */
        $build = new stdclass();
        $build->job         = $job->id;
        $build->name        = $job->name;
        $build->createdBy   = $this->app->user->account;
        $build->createdDate = $now;
        $build->updateDate  = $now;

        if($job->triggerType == 'schedule') $build->atTime = $job->atTime;

        if($job->triggerType == 'tag')
        {
            $lastTag = $this->getLastTagByRepo($repo);
            if($lastTag)
            {
                $build->tag   = $lastTag;
                $job->lastTag = $lastTag;
                $this->dao->update(TABLE_JOB)->set('lastTag')->eq($lastTag)->where('id')->eq($job->id)->exec();
            }
        }

        $this->dao->insert(TABLE_COMPILE)->data($build)->exec();
        $compileID = $this->dao->lastInsertId();

        if($job->engine == 'jenkins') $compile = $this->execJenkinsPipeline($job, $repo, $compileID);
        if($job->engine == 'gitlab')  $compile = $this->execGitlabPipeline($job);

        $this->dao->update(TABLE_COMPILE)->data($compile)->where('id')->eq($compileID)->exec();

        $this->dao->update(TABLE_JOB)
            ->set('lastExec')->eq($now)
            ->set('lastStatus')->eq($compile->status)
            ->where('id')->eq($job->id)
            ->exec();

        return $compile;
    }

    /**
     * Exec jenkins  pipeline.
     *
     * @param  object    $job
     * @param  object    $repo
     * @param  int       $compileID
     * @access public
     * @return object
     */
    public function execJenkinsPipeline($job, $repo, $compileID)
    {
        $pipeline = new stdclass();
        $pipeline->PARAM_TAG   = '';
        $pipeline->ZENTAO_DATA = "compile={$compileID}";
        if($job->triggerType == 'tag') $pipeline->PARAM_TAG = $job->lastTag;

        /* Add custom parameters to the data. */
        foreach(json_decode($job->customParam) as $paramName => $paramValue)
        {
            $paramValue = str_replace('$zentao_version',  $this->config->version, $paramValue);
            $paramValue = str_replace('$zentao_account',  $this->app->user->account, $paramValue);
            $paramValue = str_replace('$zentao_product',  $job->product, $paramValue);
            $paramValue = str_replace('$zentao_repopath', $repo->path, $paramValue);

            $pipeline->$paramName = $paramValue;
        }

        $url = $this->loadModel('compile')->getBuildUrl($job);

        $compile = new stdclass();
        $compile->id     = $compileID;
        $compile->queue  = $this->loadModel('ci')->sendRequest($url->url, $pipeline, $url->userPWD);
        $compile->status = $compile->queue ? 'created' : 'create_fail';

        return $compile;
    }

    /**
     * Exec gitlab pipeline.
     *
     * @param  int    $job
     * @access public
     * @return void
     */
    public function execGitlabPipeline($job)
    {
        $pipeline = json_decode($job->pipeline);

        $pipelineParams = new stdclass;
        $pipelineParams->ref = $pipeline->reference;

        $customParams = json_decode($job->customParam);
        $variables    = array();
        foreach($customParams as $paramName => $paramValue)
        {
            $variable = array();
            $variable['key']           = $paramName;
            $variable['value']         = $paramValue;
            $variable['variable_type'] = "env_var";

            $variables[] = $variable;
        }

        if(!empty($variables)) $pipelineParams->variables = $variables;

        $compile = new stdclass;

        $pipeline = $this->loadModel('gitlab')->apiCreatePipeline($job->server, $pipeline->project, $pipelineParams);
        if(empty($pipeline->id)) $compile->status = 'create_fail';

        if(!empty($pipeline->id))
        {
            $compile->queue  = $pipeline->id;
            $compile->status = zget($pipeline, 'status', 'create_fail');
        }

        return $compile;
    }

    /**
     * Get last tag of one repo.
     *
     * @param  object    $repo
     * @access public
     * @return void
     */
    public function getLastTagByRepo($repo)
    {
        if($repo->SCM == 'Subversion')
        {
            $dirs = $this->loadModel('svn')->getRepoTags($repo, $job->svnDir);
            if($dirs)
            {
                end($dirs);
                $lastTag = current($dirs);
                return rtrim($repo->path , '/') . '/' . trim($job->svnDir, '/') . '/' . $lastTag;
            }
        }
        else
        {
            $tags = $this->loadModel('git')->getRepoTags($repo);
            if($tags)
            {
                end($tags);
                return current($tags);
            }
        }

        return '';
    }
}
