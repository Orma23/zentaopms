<?php
/**
 * The view of doc module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     doc
 * @version     $Id: view.html.php 975 2010-07-29 03:30:25Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php echo css::internal($keTableCSS);?>
<style>.detail-content .file-image {padding: 0 50px 0 10px;}</style>
<?php $browseLink = $this->session->docList ? $this->session->docList : inlink('browse', 'browseType=byediteddate');?>
<?php
$sessionString  = $config->requestType == 'PATH_INFO' ? '?' : '&';
$sessionString .= session_name() . '=' . session_id();
?>
<?php
js::set('fullscreen', $lang->fullscreen);
js::set('retrack', $lang->retrack);
js::set('sysurl', common::getSysUrl());
js::set('docID', $doc->id);
?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
    <?php echo html::a($browseLink, "<i class='icon icon-back icon-sm'></i> " . $lang->goback, '', "class='btn btn-primary'");?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $doc->id;?></span><span class="text" title='<?php echo $doc->title;?>'><?php echo $doc->title;?></span>
      <?php if($doc->deleted):?>
      <span class='label label-danger'><?php echo $lang->doc->deleted;?></span>
      <?php endif; ?>
      <?php if($doc->version > 1):?>
      <?php
      $versions = array();
      $i = 1;
      foreach($actions as $action)
      {
          if($action->action == 'created' or $action->action == 'deletedfile' or $action->action == 'commented')
          {
              $versions[$i] =  "#$i " . zget($users, $action->actor) . ' ' . substr($action->date, 2, 14);
              $i++;
          }
          elseif($action->action == 'edited')
          {
              foreach($action->history as $history)
              {
                  if($history->field == 'content')
                  {
                      $versions[$i] = "#$i " . zget($users, $action->actor) . ' ' . substr($action->date, 2, 14);
                      $i++;
                      break;
                  }
              }
          }
      }
      krsort($versions);
      ?>
      <small class='dropdown'>
        <a href='#' data-toggle='dropdown' class='text-muted'><?php echo '#' . $version;?> <span class='caret'></span></a>
        <ul class='dropdown-menu'>
          <?php
          foreach($versions as $i => $versionTitle)
          {
              $class = $i == $version ? " class='active'" : '';
              echo '<li' . $class .'>' . html::a(inlink('view', "docID=$doc->id&version=$i&from={$lang->navGroup->doc}"), $versionTitle) . '</li>';
          }
          ?>
        </ul>
      </small>
      <?php endif; ?>
    </div>
  </div>
  <?php if(!isonlybody()):?>
  <div class='btn-toolbar pull-right'>
    <button type='button' class='btn btn-secondary fullscreen-btn' title='<?php echo $lang->retrack;?>'><i class='icon icon-fullscreen'></i><?php echo ' ' . $lang->retrack;?></button>
  </div>
  <?php endif;?>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail no-padding">
        <div class="detail-content article-content no-margin no-padding">
          <?php
          if($doc->type == 'url')
          {
              $url = $doc->content;
              if(!preg_match('/^https?:\/\//', $doc->content)) $url = 'http://' . $url;
              $urlIsHttps = strpos($url, 'https://') === 0;
              $serverIsHttps = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https'));
              if(($urlIsHttps and $serverIsHttps) or (!$urlIsHttps and !$serverIsHttps))
              {
                  echo "<iframe width='100%' id='urlIframe' src='$url'></iframe>";
              }
              else
              {
                  $parsedUrl = parse_url($url);
                  $urlDomain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

                  $title    = '';
                  $response = common::http($url);
                  preg_match_all('/<title>(.*)<\/title>/Ui', $response, $out);
                  if(isset($out[1][0])) $title = $out[1][0];

                  echo "<div id='urlCard'>";
                  echo "<div class='url-icon'><img src='{$urlDomain}/favicon.ico' width='45' height='45' /></div>";
                  echo "<div class='url-content'>";
                  echo "<div class='url-title'>{$title}</div>";
                  echo "<div class='url-href'>" . html::a($url, $url, '_target') . "</div>";
                  echo "</div></div>";
              }
          }
          else
          {
              echo $doc->content;
          }
          ?>

          <?php foreach($doc->files as $file):?>
          <?php if(in_array($file->extension, $config->file->imageExtensions)):?>
          <div class='file-image'>
            <a href="<?php echo $file->webPath?>" target="_blank">
              <img onload="setImageSize(this, 0)" src="<?php echo $this->createLink('file', 'read', "fileID={$file->id}");?>" alt="<?php echo $file->title?>" title="<?php echo $file->title;?>">
            </a>
            <span class='right-icon'>
              <?php if(common::hasPriv('file', 'download')) echo html::a($this->createLink('file', 'download', 'fileID=' . $file->id) . $sessionString, "<i class='icon icon-import'></i>", '', "class='btn-icon' style='margin-right: 10px;' title=\"{$lang->doc->download}\"");?>
              <?php if(common::hasPriv('doc', 'deleteFile')) echo html::a('###', "<i class='icon icon-trash'></i>", '', "class='btn-icon' title=\"{$lang->doc->deleteFile}\" onclick='deleteFile($file->id)'");?>
            </span>
          </div>
          <?php unset($doc->files[$file->id]);?>
          <?php endif;?>
          <?php endforeach;?>
        </div>
      </div>
      <?php echo $this->fetch('file', 'printFiles', array('files' => $doc->files, 'fieldset' => 'true', 'object' => $doc));?>
    </div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
        if(!$doc->deleted)
        {
            common::printIcon('doc', 'edit', "docID=$doc->id", $doc);
            common::printIcon('doc', 'delete', "docID=$doc->id&confirm=no", $doc, 'button', 'trash', 'hiddenwin');
        }
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4 hidden">
    <?php if(!empty($doc->digest)):?>
    <div class="cell">
      <details class="detail" open>
        <summary class="detail-title"><?php echo $lang->doc->digest;?></summary>
        <div class="detail-content">
          <?php echo !empty($doc->digest) ? $doc->digest : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </details>
    </div>
    <?php endif;?>
    <div class="cell">
      <details class="detail" open>
        <summary class="detail-title"><?php echo $lang->doc->keywords;?></summary>
        <div class="detail-content">
          <?php echo !empty($doc->keywords) ? $doc->keywords : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </details>
    </div>
    <div class="cell">
      <details class="detail" open>
        <summary class="detail-title"><?php echo $lang->doc->basicInfo;?></summary>
        <div class="detail-content">
          <table class="table table-data">
            <tbody>
              <?php if($doc->productName):?>
              <tr>
                <th class='w-90px'><?php echo $lang->doc->product;?></th>
                <td><?php echo $doc->productName;?></td>
              </tr>
              <?php endif;?>
              <?php if($doc->executionName):?>
              <tr>
                <th class='w-80px'><?php echo $lang->doc->execution;?></th>
                <td><?php echo $doc->executionName;?></td>
              </tr>
              <?php endif;?>
              <tr>
                <th class='w-80px'><?php echo $lang->doc->lib;?></th>
                <td><?php echo $lib->name;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->doc->module;?></th>
                <td><?php echo $doc->moduleName ? $doc->moduleName : '/';?></td>
              </tr>
              <tr>
                <th><?php echo $lang->doc->addedDate;?></th>
                <td><?php echo $doc->addedDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->doc->editedBy;?></th>
                <td><?php echo zget($users, $doc->editedBy);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->doc->editedDate;?></th>
                <td><?php echo $doc->editedDate;?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </details>
    </div>
    <div class='cell'>
      <?php
      $canBeChanged = common::canBeChanged('doc', $doc);
      if($canBeChanged) $actionFormLink = $this->createLink('action', 'comment', "objectType=doc&objectID=$doc->id");
      ?>
      <?php include '../../common/view/action.html.php';?>
    </div>
  </div>
</div>

<div id="mainActions" class='main-actions'>
  <?php common::printPreAndNext($preAndNext);?>
</div>
<?php js::set('canDeleteFile', common::hasPriv('doc', 'deleteFile'));?>
<?php include '../../common/view/syntaxhighlighter.html.php';?>
<?php include '../../common/view/footer.html.php';?>
