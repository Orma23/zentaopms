<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php if(isset($config->maxVersion) or isset($config->proVersion) or isset($config->bizVersion)):?>
<style>#mainContent > .side-col.col-lg{width: 210px}</style>
<style>.hide-sidebar #sidebar{width: 0 !important}</style>
<?php endif;?>
<?php js::set('weekend', $config->execution->weekend);?>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg' id='sidebar'>
    <?php include 'blockreportlist.html.php';?>
  </div>
  <div class='main-col'>
    <div class='cell'>
      <form method='post'>
        <div class="row" id='conditions'>
          <div class='col-sm-2'>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->report->dept;?></span>
              <?php echo html::select('dept', $depts, $dept, "class='form-control chosen' onchange='changeParams(this)'");?>
            </div>
          </div>
          <div class='col-sm-4'>
            <div class='input-group input-group-sm'>
              <span class='input-group-addon'><?php echo $lang->report->beginAndEnd;?></span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('begin', $begin, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'");?></div>
              <span class='input-group-addon fix-border'><?php echo $lang->report->to;?></span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('end', $end, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'");?></div>
            </div>
          </div>
          <div class='col-sm-2'>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->report->diffDays;?></span>
              <?php echo html::input('days', $days, "class='form-control' style='text-align:center'");?>
            </div>
          </div>
          <div class='col-sm-4'>
            <div class="row">
              <div class="col-sm-5">
                <div class='input-group'>
                  <span class='input-group-addon'><?php echo $lang->report->workday;?></span>
                  <?php echo html::input('workday', $workday, "class='form-control'");?>
                </div>
              </div>
              <div class="col-sm-4">
                <?php echo html::select('assign', $lang->report->assign, $assign, "class='form-control' onchange='changeParams(this)'");?>
              </div>
              <div class="col-sm-3">
                <?php echo html::submitButton($lang->report->query, '', 'btn btn-primary btn-block');?>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <?php if(empty($workload)):?>
    <div class="cell">
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
      </div>
    </div>
    <?php else:?>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title"><?php echo $title;?></div>
          <nav class="panel-actions btn-toolbar"></nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id="workload">
            <thead>
              <tr class='colhead text-center'>
                <th class="c-user"><?php echo $lang->report->user;?></th>
                <th><?php echo $lang->report->execution;?></th>
                <th class="c-count"><?php echo $lang->report->task;?></th>
                <th class="c-hours"><?php echo $lang->report->remain;?></th>
                <th class="c-count"><?php echo $lang->report->taskTotal;?></th>
                <th class="c-hours"><?php echo $lang->report->manhourTotal;?></th>
                <th class="c-workload"><?php echo $lang->report->workloadAB;?></th>
              </tr>
            </thead>
            <tbody>
              <?php $color = false;?>
              <?php foreach($workload as $account => $load):?>
              <?php if(!isset($users[$account])) continue;?>
              <tr class="text-center">
                <td rowspan="<?php echo count($load['task']);?>"><?php echo $users[$account];?></td>
                <?php $id = 1;?>
                <?php foreach($load['task'] as $execution => $info):?>
                <?php $class = $color ? 'rowcolor' : '';?>
                <?php if($id != 1) echo '<tr class="text-center">';?>
                <td title='<?php echo $execution?>' class="<?php echo $class;?> text-left"><?php echo html::a($this->createLink('execution', 'view', "executionID={$info['executionID']}"), $execution);?></td>
                <td class="<?php echo $class;?>"><?php echo $info['count'];?></td>
                <td class="<?php echo $class;?>"><?php echo $info['manhour'];?></td>
                <?php if($id == 1):?>
                <td rowspan="<?php echo count($load['task']);?>"><?php echo $load['total']['count'];?></td>
                <td rowspan="<?php echo count($load['task']);?>"><?php echo $load['total']['manhour'];?></td>
                <td rowspan="<?php echo count($load['task']);?>"><?php echo round($load['total']['manhour'] / $allHour * 100, 2) . '%';?></td>
                <?php endif;?>
                <?php if($id != 1) echo '</tr>'; $id ++;?>
                <?php $color = !$color;?>
                <?php endforeach;?>
              </tr>
            <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
