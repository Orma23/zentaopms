<?php
/**
 * The create view of build module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: create.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->build->create;?></h2>
    </div>
    <form class='load-indicator main-form form-ajax' id='dataform' method='post' enctype='multipart/form-data'>
      <table class='table table-form'>
        <?php if($app->tab == 'project'):?>
        <tr>
          <th><?php echo $lang->executionCommon;?></th>
          <td><?php echo html::select('execution', $executions, $executionID, "onchange='loadProducts(this.value);' class='form-control chosen' required");?></td>
        </tr>
        <?php endif;?>
        <tr>
          <th><?php echo $lang->build->product;?></th>
          <?php if(!empty($products)):?>
          <td>
            <div class='input-group' id='productBox'>
              <?php echo html::select('product', $products, empty($product) ? '' : $product->id, "onchange='loadBranches(this.value);' class='form-control chosen' required");?>
              <?php
              if(!empty($product) and $product->type != 'normal' and isset($branches[$product->branch]))
              {
                  if($product->branch) $branches = array($product->branch => $branches[$product->branch]);
                  echo "<span class='input-group-addon fix-padding fix-border'></span>" . html::select('branch', $branches, $product->branch, "class='form-control chosen'");
              }
              ?>
            </div>
          </td>
          <?php else:?>
          <td>
            <div class='input-group' id='productBox'>
              <?php printf($lang->build->noProduct, $this->createLink('execution', 'manageproducts', "executionID=$executionID&from=buildCreate", '', 'true'), $app->tab);?>
            </div>
          </td>
          <?php endif;?>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->build->name;?></th>
          <td><?php echo html::input('name', '', "class='form-control' required");?></td>
          <td class='text-muted'>
            <?php if($lastBuild):?>
            <div class='help-block'> &nbsp; <?php echo $lang->build->last . ': <a class="code label label-badge label-light" id="lastBuildBtn">' . $lastBuild->name . '</a>';?></div>
            <?php endif;?>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->build->builder;?></th>
          <td><?php echo html::select('builder', $users, $app->user->account, 'class="form-control chosen" required');?></td>
        </tr>
        <tr>
          <th><?php echo $lang->build->date;?></th>
          <td><?php echo html::input('date', helper::today(), "class='form-control form-date' required");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->build->scmPath;?></th>
          <td colspan='2'><?php echo html::input('scmPath', '', "class='form-control' placeholder='{$lang->build->placeholder->scmPath}'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->build->filePath;?></th>
          <td colspan='2'><?php echo html::input('filePath', '', "class='form-control' placeholder='{$lang->build->placeholder->filePath}'");?></td>
        </tr>
        <?php $this->printExtendFields('', 'table', 'columns=2');?>
        <tr>
          <th><?php echo $lang->build->files;?></th>
          <td colspan='2'><?php echo $this->fetch('file', 'buildForm');?></td>
        </tr>
        <tr>
          <th><?php echo $lang->build->desc;?></th>
          <td colspan='2'><?php echo html::textarea('desc', '', "rows='10' class='form-control kindeditor' hidefocus='true'");?></td>
        </tr>
        <tr>
          <td colspan="3" class="text-center form-actions">
            <?php echo html::submitButton();?>
            <?php echo html::backButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php js::set('productGroups', $productGroups)?>
<?php include '../../common/view/footer.html.php';?>
