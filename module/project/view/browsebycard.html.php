<?php
/**
 * The browsebycard view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     project
 * @version     $Id: browsebycard.html.php 4769 2021-07-23 11:29:21Z $
 * @link        https://www.zentao.net
 */
?>
<style>
#cards {margin: 0 0;}
#cards > .col {width: 25%;}
@media screen and (max-width: 1500px) {#cards > .col {width: 33.3333333%;}}
#cards .panel {margin: 10px 0;  border: 1px solid #DCDCDC; border-radius: 4px; box-shadow: none; cursor: pointer; height: 190px;}
#cards .pager .btn {padding-top: 4px;}
#cards .panel:hover {border-color: #006AF1; box-shadow: 0 0 10px 0 rgba(0,0,100,.25);}
#cards .panel .projectStatus .label{float: right;}
#cards .panel-heading {padding: 12px 24px 10px 16px;}
#cards .panel-body {padding: 0 16px 16px;}
#cards .project-type-label {padding: 1px 0px;}
#cards .icon {font-size: 8px;}
#cards .project-name {font-size: 14px; display: inline-block; max-width: 70%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle;}
#cards .project-infos {font-size: 12px;}
#cards .project-infos > span {display: inline-block; line-height: 12px; border: 1px solid #E4E9F7;}
#cards .project-infos > span > .icon {font-size: 12px; display: inline-block; position: relative; top: -1px}
#cards .project-infos > span + span {margin-left: 15px;}
#cards .project-infos > .budget {max-width: 75px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
#cards .project-detail {position: absolute; top: 75px; left: 16px; right: 16px; font-size: 12px;}
#cards .project-footer {position: absolute; bottom: 10px; right: 10px; left: 15px;}
#cards .pager {margin: 0; float: right;}
#cards .pager .btn {border: none}
#cards .panel .label-wait {background: #EFEFEF !important; color: #838A9D;}
#cards .panel .label-doing {background: #E9F2FB !important; color: #2B80FF;}
#cards .panel .label-suspended {background: #AAA !important; color: #FFF;}
#cards .panel .label-closed {background: #D4F7F9 !important; color: #00A78E;}
#cards .panel .label-delay {background: #F85A40 !important; color: #FFF;}
#cards .project-infos .text-red {color: #F85A40 !important;}
#cards .project-detail .progress-pie {width: 24px; position: absolute; top: 18px;}
#cards .project-detail  .leftTasks, .totalLeft {display:block; margin-top: 8px;}
#cards .project-members {float: left; height: 24px; line-height: 24px;}
#cards .project-members > a {display: inline-block; height: 24px;}
#cards .project-members > a + a {margin-left: -5px;}
#cards .project-members > a > .avatar {display: inline-block; width: 24px; height: 24px; line-height: 24px; margin-right: 1px;}
#cards .project-members > span {display: inline-block; color: transparent; width: 2px; height: 2px; background-color: #8990a2; position: relative; border-radius: 50%; top: 3px; margin: 0 3px;}
#cards .project-members > span:before,
#cards .project-members > span:after {content: ''; display: block; position: absolute; width: 2px; height: 2px; background-color: #8990a2; top: 0; border-radius: 50%}
#cards .project-members > span:before {left: -4px;}
#cards .project-members > span:after {right: -4px;}
#cards .project-members-total {display: inline-block; margin-left: 6px; position: relative; top: 3px}
#cards .project-actions {position: absolute; right: -8px; bottom: -5px; white-space: nowrap;}
#cards .project-actions .dropdown-menu {padding: 5px 6px; top: -5px; right: 30px;}
#cards .icon-ellipsis-v {font-size: 13px;}
#cards .teamTitle {margin-bottom: 30px;}
</style>
<div id="mainMenu" class="clearfix table-row">
  <div class="btn-toolBar pull-left">
    <div class="input-control w-150px" id='programBox'><?php echo html::select('programID', $programs, $programID, "onchange=changeProgram(this.value) class='form-control chosen' data-placeholder='{$lang->project->selectProgram}' data-drop_width='450' data-max_drop_width='0'");?></div>
    <?php foreach($lang->project->featureBar as $key => $label):?>
    <?php $active = $browseType == $key ? 'btn-active-text' : '';?>
    <?php $label = "<span class='text'>$label</span>";?>
    <?php if($browseType == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
    <?php echo html::a(inlink('browse', "programID=$programID&browseType=$key"), $label, '', "class='btn btn-link $active'");?>
    <?php endforeach;?>
    <?php echo html::checkbox('involved', array('1' => $lang->project->mine), '', $this->cookie->involved ? 'checked=checked' : '');?>
  </div>
  <div class="btn-toolbar pull-right">
    <div class="btn-group panel-actions">
      <?php echo html::a('#',"<i class='icon-list'></i> &nbsp;", '', "class='btn btn-icon' title='{$lang->project->bylist}' id='switchButton' data-type='bylist'");?>
      <?php echo html::a('#',"<i class='icon-cards-view'></i> &nbsp;", '', "class='btn btn-icon text-primary' title='{$lang->project->bycard}' id='switchButton' data-type='bycard'");?>
    </div>
    <?php common::printLink('project', 'export', "status=$browseType&orderBy=$orderBy", "<i class='icon-export muted'> </i>" . $lang->export, '', "class='btn btn-link export'")?>
    <?php if(isset($this->config->maxVersion) and !defined('TUTORIAL')):?>
    <?php common::printLink('project', 'createGuide', "programID=$programID", '<i class="icon icon-plus"></i> ' . $lang->project->create, '', 'class="btn btn-primary create-project-btn" data-toggle="modal" data-target="#guideDialog"');?>
    <?php else:?>
    <?php common::printLink('project', 'create', 'mode=scrum', '<i class="icon icon-plus"></i> ' . $lang->project->create, '', 'class="btn btn-primary create-project-btn"');?>
    <?php endif;?>
  </div>
</div>
<div id='mainContent'>
  <div class='row cell' id='cards'>
    <?php if(empty($projectStats)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->project->empty;?></span>
        <?php if(isset($this->config->maxVersion) and !defined('TUTORIAL')):?>
        <?php common::printLink('project', 'createGuide', "programID=$programID", '<i class="icon icon-plus"></i> ' . $lang->project->create, '', 'class="btn btn-info" data-toggle="modal" data-target="#guideDialog"');?>
        <?php elseif($this->config->systemMode == 'new'):?>
        <?php common::printLink('project', 'create', 'mode=scrum', '<i class="icon icon-plus"></i> ' . $lang->project->create, '', 'class="btn btn-info"');?>
        <?php else:?>
        <?php common::printLink('execution', 'create', '', '<i class="icon icon-plus"></i> ' . $lang->execution->create, '', 'class="btn btn-info"');?>
        <?php endif;?>
      </p>
    </div>
    <?php else:?>
    <?php foreach ($projectStats as $projectID => $project):?>
    <div class='col' data-id='<?php echo $projectID?>'>
      <div class='panel'>
        <div class='projectStatus'>
          <?php $status = isset($project->delay) ? 'delay' : $project->status;?>
          <span class="label label-<?php echo $status;?>"><?php echo $lang->project->statusList[$status];?></span>
        </div>
        <div class='panel-heading'>
          <?php if(isset($config->maxVersion) and $project->model === 'waterfall'):?>
          <span class='project-type-label label label-warning label-outline'><i class='icon icon-waterfall'></i></span>
          <?php elseif(isset($config->maxVersion)):?>
          <span class='project-type-label label label-info label-outline'><i class='icon icon-sprint'></i></span>
          <?php endif;?>
          <strong class='project-name' title='<?php echo $project->name;?>'><?php echo html::a(helper::createLink('project', 'index', "projectID=$projectID"), $project->name);?></strong>
        </div>
        <div class='panel-body'>
          <div class='project-infos'>
            <?php
            $projectBudget = in_array($app->getClientLang(), array('zh-cn','zh-tw')) ? round((float)$project->budget / 10000, 2) . $lang->project->tenThousand : round((float)$project->budget, 2);
            $budgetTitle   = $project->budget != 0 ? zget($lang->project->currencySymbol, $project->budgetUnit) . ' ' . $projectBudget : $lang->project->budget . $lang->project->future;
            $project->end  = $project->end == LONG_TIME ? $this->lang->project->longTime : $project->end;
            $project->date = str_replace('-', '.', $project->begin) . ' - ' . str_replace('-', '.', $project->end);
            $canActions = (common::hasPriv('project','edit') or common::hasPriv('project','start') or common::hasPriv('project','activate') or common::hasPriv('project','suspend') or common::hasPriv('project','close'));
            ?>
            <span title="<?php echo $budgetTitle;?>" class='label label-outline budget'><?php echo $budgetTitle;?></span>
            <span title="<?php echo $project->date;?>" class="label label-outline <?php echo $status == 'delay' ? 'text-red' : '';?>"><?php echo $project->date;?></span>
          </div>
          <div class='project-detail'>
            <div class='row'>
              <div class='col-xs-4'>
                <div><?php echo $lang->project->progress;?></div>
                <div class='progress-pie' data-doughnut-size='90' data-color='#3CB371' data-value="<?php echo $project->hours->progress?>" data-width='24' data-height='24' data-back-color='#e8edf3'>
                  <div class='progress-info'><?php echo $project->hours->progress;?></div>
                </div>
              </div>
              <div class='col-xs-4'>
                <span><?php echo $lang->project->leftTasks;?></span>
                <span class='leftTasks' title="<?php echo $project->leftTasks;?>"><?php echo $project->leftTasks;?></span>
              </div>
              <div class='col-xs-4'>
                <span><?php echo $lang->project->leftHours;?></span>
                <span class='totalLeft' title="<?php echo empty($project->hours->totalLeft) ? '—' : $project->hours->totalLeft . $lang->execution->workHour;?>"><?php echo empty($project->hours->totalLeft) ? '—' : $project->hours->totalLeft . $lang->execution->workHourUnit;?></span>
              </div>
            </div>
          </div>
          <div class='project-footer'>
            <?php $titleClass = ($project->teamCount == 0 and !$canActions) ? 'teamTitle' : '';?>
            <?php $count      = 0;?>
            <div class="<?php echo $titleClass?>"><?php echo $lang->project->teamMember;?></div>
            <div class="clearfix">
              <?php if(!empty($project->teamMembers)):?>
              <div class='project-members pull-left'>
                <?php foreach($project->teamMembers as $member):?>
                <?php
                if($count > 2) continue;
                if(!isset($users[$member]))
                {
                    $project->teamCount --;
                    continue;
                }
                $count ++;
                ?>
                <a href='<?php echo helper::createLink('project', 'team', "projectID=$projectID");?>' title="<?php echo $users[$member];?>">
                  <?php echo html::smallAvatar(array('avatar' => $usersAvatar[$member], 'account' => $member)); ?>
                </a>
                <?php endforeach;?>
                <?php if($project->teamCount > 3):?>
                <?php echo '<span>…</span>';?>
                <a href='<?php echo helper::createLink('project', 'team', "projectID=$projectID");?>' title="<?php echo $users[$member];?>">
                  <?php echo html::smallAvatar(array('avatar' => $usersAvatar[end($project->teamMembers)], 'account' => $member)); ?>
                </a>
                <?php endif;?>
              </div>
              <?php endif;?>
              <div class='project-members-total pull-left'><?php echo html::a(helper::createLink('project', 'team', "projectID=$projectID"), sprintf($lang->project->teamSumCount, $project->teamCount));?></div>
            </div>
            <div class='project-actions'>
              <div class='dropdown'>
                <?php if($canActions):?>
                <?php echo html::a('javascript:;', "<i class='icon icon-ellipsis-v'></i>", '', "data-toggle='dropdown' class='btn btn-link'");?>
                <ul class='dropdown-menu pull-right'>
                  <?php
                  common::printIcon('project', 'edit',     "projectID=$project->id", $project, 'list', 'edit',  '', 'btn-action');
                  common::printIcon('project', 'start',    "projectID=$project->id", $project, 'list', 'play',  '', 'iframe btn-action', true);
                  common::printIcon('project', 'suspend',  "projectID=$project->id", $project, 'list', 'pause', '', 'iframe btn-action', true);
                  common::printIcon('project', 'close',    "projectID=$project->id", $project, 'list', 'off',   '', 'iframe btn-action', true);
                  common::printIcon('project', 'activate', "projectID=$project->id", $project, 'list', 'magic', '', 'iframe btn-action', true);
                  ?>
                </ul>
                <?php endif;?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach;?>
    <div class='col-xs-12' id='cardsFooter'>
      <?php $pager->show('right', 'pagerjs');?>
    </div>
    <?php endif;?>
  </div>
</div>
<script>
$('.progress-pie:visible').progressPie();
</script>
