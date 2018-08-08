<?php echo $header;?>
<link rel="stylesheet" href="resources/css/jquery-ui.css">

<div class="content">
    <div class="header">
        <h1 class="page-title">List Activity</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">Home</a> <span class="divider">/</span></li>
        
    </ul>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="btn-toolbar">
                <a href="add_activity.php" class="btn btn-primary"><i class="icon-plus"></i> New Activity</a>
                <div class="btn-group"></div>
            </div>
            <div class="searchfrm">
                <form name="frmsearch" id="frmsearch" method="post" action="listactivity.php">
                    <div class="control-group serhinput">
                        <label class="control-label" for="textinput">Start Date</label>
                        <div class="controls">
                            <input id="st_date" name="st_date" placeholder="Enter Start Date" class="input-xlarge" type="text" data-required="true" autocomplete="off" readonly="">
                        </div>
                    </div>
                    <div class="control-group serhinput">
                        <label class="control-label" for="textinput">End Date</label>
                        <div class="controls">
                            <input id="ed_date" name="ed_date" placeholder="Enter End Date" class="input-xlarge" type="text" data-required="true" autocomplete="off" readonly="">
                        </div>
                    </div>
                     <div class="control-group serhinput">
                        <label class="control-label" for="">Activity</label>
                        <div class="controls">
                            <input id="activity_name" name="activity_name" placeholder="Enter Activity" class="input-xlarge" type="text" data-required="true">
                        </div>
                    </div>
                    <div class="control-group serhinput">
                        <label class="control-label" for="">Status</label>
                        <div class="controls">
                            <input id="status" name="status" placeholder="Enter Status" class="input-xlarge" type="text" data-required="true">
                        </div>
                    </div>
                    <div class="srhdiv">
                       <input type="submit" class="btn btn-primary"  name="search" id="btnsearch" value="Search">
                    </div>
                    <div class="clearfix"></div>
                 </form>
            </div>
			<div style="overflow: auto; width: 100%;">
                <div class="well" style="padding-top: 0px; overflow: auto;">
					<div class="#estyle#"><span id = "divmsg" ></span></div>
                    <table class="table table-striped table-hover table-condensed" id="example">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Activity</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>No of days</th>
                            <th>Year</th>
                            <th>Status</th>
							<th>Action</th>
							<th>Copy</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $record_list->fetch_assoc()){ ?>
							<tr>
								<td></td>
								<td><?php echo $row['ACTIVITY_NAME']; ?></td>
								<td class="dateinline"><?php echo  date('Y-m-d',$row['ACTIVITY_START_DATE']); ?></td>
								<td class="dateinline"><?php echo  date('Y-m-d',$row['ACTIVITY_END_DATE']); ?></td>
								<td><?php echo (($row['ACTIVITY_END_DATE'] - $row['ACTIVITY_START_DATE']) / (60 * 60 * 24)); ?></td>
								<td><?php echo  date('Y',$row['ACTIVITY_END_DATE']); ?></td>
                                <td>
                                   <?php if(time()>$row['ACTIVITY_END_DATE']){
                                       echo "Completed";
                                   }else{
                                       echo "Running";
                                   } ?>
                                </td>
								<td><a href="edit_activity.php?update_id=<?php echo $row['ACTIVITY_TYPE_ID'];?>"><i class="icon-pencil"></i></a></td>
								<td><a href="add_activity.php?id=<?php echo $row['ACTIVITY_TYPE_ID'];?>" class="btn btn-primary">Copy</a></td>
                            </tr>
                            <?php } ?>
						</tbody>
                    </table>
                </div>
            </div>
            <div class="pagination">
                <ul>
                    <?php
                     if($total_count['total'] % 2 == 0){
                        $total_pages = $total_count['total']/2;
                     } else {
                        $total_pages = floor($total_count['total']/2)+1; 
                     }?>
                     <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==1){echo 'javascript:void(0)';} else {echo 'listactivity.php?search='.$search.'&&page=1';}?>' class="<?php if(!isset($_GET['page']) || $_GET['page']==1){echo 'hidden';} ?>" id="first_page">First Page</a> </li>
                     <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==1){echo 'javascript:void(0)';} else if(!isset($_GET['page'])){echo 'javascript:void(0)';} else { echo 'listactivity.php?search='.$search.'&&page='.($_GET['page']-1);}?>' class="<?php if(!isset($_GET['page']) || $_GET['page']==1){echo 'hidden';} ?>">Prev Page</a> </li>
                    <?php for($i=1;$i<=$total_pages;$i++){
                        ?>
                        <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==$i){echo 'javascript:void(0)';} else if(!isset($_GET['page']) && $i==1){echo 'javascript:void(0)';} else { echo 'listactivity.php?search='.$search.'&&page='.$i;}?>' style ="<?php if(isset($_GET['page']) && $_GET['page']==$i){echo 'color:#000;';} else if(!isset($_GET['page']) && $i==1){echo 'color:#000;';} ?> ?>"><?php echo $i; ?></a></li>
                     <?php }?>
                     <li><a href='<?php if($total_pages<=1){echo 'javascript:void(0)';} else if(isset($_GET['page']) && $_GET['page']==($total_pages)){echo 'javascript:void(0)';} else if(!isset($_GET['page'])){echo 'listactivity.php?search='.$search.'&&page=2';} else { echo 'listactivity.php?search='.$search.'&&page='.($_GET['page']+1);}?>' class="<?php if((isset($_GET['page']) && $_GET['page']==$total_pages)||$total_pages==1){echo 'hidden';} ?>" >Next</a></li>
                     <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==($total_pages)){echo 'javascript:void(0)';} else {echo 'listactivity.php?search='.$search.'&&page='.$total_pages;}?>' class="<?php if((isset($_GET['page']) && $_GET['page']==$total_pages)||$total_pages == 1){echo 'hidden';} ?>" id="last_page">Last Page</a></li>
                </ul>
            </div>
            <?php echo $footer; ?>
        </div>
    </div>
</div>
<script src="resources/lib/bootstrap/js/bootstrap.js"></script>
<script src="resources/js/jquery-ui.js"></script>
<script type="text/javascript">
    $(function() {
        $("#st_date").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });

        $("#ed_date").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
</script>
</body>
</html>