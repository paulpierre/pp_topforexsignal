<?php
  /**
   * Login Help
   *
   * @package Membership Manager Pro
   * @author wojoscripts.com
   * @copyright 2014
   * @version $Id: fields.php, v2.00 2014-08-10 10:12:05 gewa Exp $
   */
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
?>
<?php switch(Filter::$action): case "edit": ?>
<?php $row = Core::getRowById(Core::fTable, Filter::$id);?>
<p class="greentip"><i class="icon-lightbulb icon-3x pull-left"></i> Here you can update your custom field.<br>
  Fields marked <i class="icon-append icon-asterisk"></i> are required.</p>
<form class="xform" id="admin_form" method="post">
  <header>Custom Fields<span>Editing Field <i class="icon-double-angle-right"></i> <?php echo $row->title;?></span></header>
  <div class="row">
    <section class="col col-6">
      <label class="input"> <i class="icon-append icon-asterisk"></i>
        <input type="text" name="title" value="<?php echo $row->title;?>">
      </label>
      <div class="note note-error">Field Name</div>
    </section>
    <section class="col col-6">
      <label class="input">
        <input type="text" name="tooltip" value="<?php echo $row->tooltip;?>">
      </label>
      <div class="note">Field Tooltip</div>
    </section>
  </div>
  <div class="row">
    <section class="col col-6">
      <select name="type">
        <?php echo Core::getFieldSection($row->type);?>
      </select>
      <div class="note">Field Section</div>
    </section>
    <section class="col col-3">
      <div class="inline-group">
        <label class="radio">
          <input type="radio" name="req" value="1" <?php getChecked($row->req, 1); ?>>
          <i></i>Yes</label>
        <label class="radio">
          <input type="radio" name="req" value="0" <?php getChecked($row->req, 0); ?>>
          <i></i>No</label>
      </div>
      <div class="note">Required</div>
    </section>
    <section class="col col-3">
      <div class="inline-group">
        <label class="radio">
          <input type="radio" name="active" value="1" <?php getChecked($row->active, 1); ?>>
          <i></i>Yes</label>
        <label class="radio">
          <input type="radio" name="active" value="0" <?php getChecked($row->active, 0); ?>>
          <i></i>No</label>
      </div>
      <div class="note">Published</div>
    </section>
  </div>
  <footer>
    <button class="button" name="dosubmit" type="submit">Update Field<span><i class="icon-ok"></i></span></button>
    <a href="index.php?do=fields" class="button button-secondary">Cancel</a> </footer>
  <input name="id" type="hidden" value="<?php echo Filter::$id;?>" />
</form>
<?php echo Core::doForm("processField");?>
<?php break;?>
<?php case"add": ?>
<p class="greentip"><i class="icon-lightbulb icon-3x pull-left"></i> Here you can add new custom field.<br>
  Fields marked <i class="icon-append icon-asterisk"></i> are required.</p>
<form class="xform" id="admin_form" method="post">
  <header>Custom Fields<span>Adding Field</span></header>
  <div class="row">
    <section class="col col-6">
      <label class="input"> <i class="icon-append icon-asterisk"></i>
        <input type="text" name="title" placeholder="Field Name">
      </label>
      <div class="note note-error">Field Name</div>
    </section>
    <section class="col col-6">
      <label class="input">
        <input type="text" name="tooltip" placeholder="Field Tooltip">
      </label>
      <div class="note">Field Tooltip</div>
    </section>
  </div>
  <div class="row">
    <section class="col col-6">
      <select name="type">
        <?php echo Core::getFieldSection();?>
      </select>
      <div class="note">Field Section</div>
    </section>
    <section class="col col-3">
      <div class="inline-group">
        <label class="radio">
          <input type="radio" name="req" value="1" >
          <i></i>Yes</label>
        <label class="radio">
          <input name="req" type="radio" value="0" checked="checked" >
          <i></i>No</label>
      </div>
      <div class="note">Required</div>
    </section>
    <section class="col col-3">
      <div class="inline-group">
        <label class="radio">
          <input name="active" type="radio" value="1" checked="checked" >
          <i></i>Yes</label>
        <label class="radio">
          <input type="radio" name="active" value="0" >
          <i></i>No</label>
      </div>
      <div class="note">Published</div>
    </section>
  </div>
  <footer>
    <button class="button" name="dosubmit" type="submit">Add Field<span><i class="icon-ok"></i></span></button>
    <a href="index.php?do=fields" class="button button-secondary">Cancel</a> </footer>
</form>
<?php echo Core::doForm("processField");?>
<?php break;?>
<?php default: ?>
<?php
    $fields = $core->getCustomFields();


    error_log('fields: ' . print_r($fields,true));
?>

<p class="greentip"><i class="icon-lightbulb icon-3x pull-left"></i>Here you can manage your custom fields.<br>
  To reorder fields click and drag item reorder handle, position it where you want it. Position will be updated automatically.</p>
<section class="widget">
  <header>
    <div class="row">
      <h1><i class="icon-reorder"></i> Viewing Custom Fields</h1>
      <aside> <a class="hint--left hint--add hint--always hint--rounded" data-hint="Add Field" href="index.php?do=fields&amp;action=add"><span class="icon-plus"></span></a> </aside>
    </div>
  </header>
  <div class="content2">
    <table class="myTable">
      <thead>
        <tr>
          <th class="header">#</th>
          <th class="header">Field Name</th>
          <th class="header">Section</th>
          <th class="header">Position</th>
          <th class="header">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!$fields):?>
        <tr>
          <td colspan="5"><?php echo Filter::msgAlert('<span>Alert!</span>You don\'t have any custom fields yet...');?></td>
        </tr>
        <?php else:?>
        <?php foreach ($fields as $row):?>
        <tr id="node-<?php echo $row->id;?>">
          <td class="id-handle"><i class="icon-reorder"></i></td>
          <td><?php echo $row->title;?></td>
          <td><?php echo Core::fieldSection($row->type);?></td>
          <td><?php echo $row->sorting;?></td>
          <td><span class="tbicon"> <a href="index.php?do=fields&amp;action=edit&amp;id=<?php echo $row->id;?>" class="tooltip" data-title="Edit"><i class="icon-pencil"></i></a> </span> <span class="tbicon"> <a id="item_<?php echo $row->id;?>" class="tooltip delete" data-rel="<?php echo $row->title;?>" data-title="Delete"><i class="icon-trash"></i></a> </span></td>
        </tr>
        <?php endforeach;?>
        <?php unset($row);?>
        <?php endif;?>
      </tbody>
    </table>
  </div>
</section>
<?php echo Core::doDelete("Delete Field","deleteField");?> 
<script type="text/javascript"> 
// <![CDATA[
$(document).ready(function () {
    $(".myTable tbody").sortable({
        helper: 'clone',
        handle: '.id-handle',
        placeholder: 'placeholder',
        opacity: .6,
        update: function (event, ui) {
            serialized = $(".myTable tbody").sortable('serialize');
            $.ajax({
                type: "POST",
                url: "controller.php?sortfields",
                data: serialized,
                success: function (msg) {}
            });
        }
    });
});
// ]]>
</script>
<?php break;?>
<?php endswitch;?>