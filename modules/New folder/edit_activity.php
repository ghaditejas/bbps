<?php echo $header; ?>
<link rel="stylesheet" href="resources/css/jquery-ui.css">
<div class="content">

    <div class="header">
        <h1 class="page-title">Edit Activity</h1>
    </div>

    <ul class="breadcrumb">
        <li><a href="index.php">Home</a> <span class="divider">/</span></li>
        <li class="active"><a href="listactivity.php">List Activity</a> <span class="divider">/</span></li>
        <li class="active">Edit Activity</li>
    </ul>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="well">
                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane active in" id="home">
                        <!-- <div class="#estyle#">#msg#</div>-->
                        <form id="frmdivision" name="frmdivision" action="edit_activity.php" method="post" class="form-horizontal" data-validate="parsley">
                            <legend style="padding-left: 10px; width: auto;">Edit Activity</legend>
                            <div class="control-group">
                                <label class="control-label" for="textinput">End Date<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="end_date" name="end_date" placeholder="Enter Payment End Date" class="input-xlarge " type="text" data-required="true" autocomplete="off" readonly="" value="<?php if(isset($_POST['end_date'])){echo $_POST['end_date'];} else { echo date('Y-m-d',$activity_type_map['ACTIVITY_END_DATE']);} ?>">
                                    <div class="validationAlert text-error"></div>
                                    <input type="hidden" id="activity_type_id" name="activity_type_id" value="<?php if(isset($_POST['activity_type_id'])){echo $_POST['activity_type_id'];} else { echo $activity_type_map['ACTIVITY_TYPE_ID']; } ?>">
                                </div>
                            </div>
                            <div class="form-actions">
                            <input type="hidden" name="submit" value="1">
                                <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php echo $footer; ?>
        </div>
    </div>
</div>
<script src="resources/lib/bootstrap/js/bootstrap.js"></script>
<script src="resources/js/jquery-ui.js"></script>

<script type="text/javascript">
    $(function() {
        $("#end_date").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
</script>
</body>

</html>