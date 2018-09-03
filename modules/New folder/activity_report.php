<?php echo $header;?>
<link rel="stylesheet" href="resources/css/jquery-ui.css">

<div class="content">
    <div class="header">
        <h1 class="page-title">Activity Report</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">Home</a> <span class="divider">/</span></li>

    </ul>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="btn-toolbar" style="<?php echo $hide;?>">
                <button class="btn btn-primary" data-toggle="collapse" data-target="#advance-search-content" aria-expanded="true" aria-controls="advance-search-content">Advanced Search</button>
                <div class="btn-group"></div>
            </div>
            <div class="searchfrm adv-search-collapsed collapse" aria-expanded="false" id="advance-search-content">
                <form name="frmsearch" id="frmsearch" method="post" action="activity_report.php">
                    <!-- <div class="control-group serhinput">
                        <div class="controls">
                            <input id="fname" name="fname" class="" placeholder="First Name" type="text" data-required="true">
                        </div>
                    </div> -->
                    <!-- <div class="control-group serhinput">
                        <div class="controls">
                            <input id="lname" name="lname" class="" placeholder="Last Name" type="text" data-required="true">
                        </div>
                    </div> -->
                    <div class="control-group serhinput">
                        <div class="controls">
                            <input id="activity_name" name="activity_name" class="" placeholder="Activity Name" type="text" data-required="true">
                        </div>
                    </div>
                    <div class="control-group serhinput">
                        <div class="controls">
                            <select name="selclass" class="" id="selclass"><option value="">Select Class</option><option value="241">4</option><option value="242">5</option><option value="243">6</option><option value="244">7</option><option value="245">8</option><option value="246">9</option><option value="248">10</option><option value="249">IV</option><option value="250">I</option><option value="251">V</option><option value="252">II</option><option value="253">Class 1</option><option value="254">Class 2</option><option value="255">Class 3</option><option value="256">Class 4</option><option value="264">Test_Class_1</option><option value="285">Jr.KG</option></select>
                        </div>
                    </div>
                    <div class="control-group serhinput">
                        <div class="controls">
                            <select class="seldiv" name="seldiv"><option value="">Select Division</option><option value="241">A</option><option value="242">B</option><option value="243">C</option><option value="244">D</option></select>
                        </div>
                    </div>
                    <div class="control-group serhinput">
                        <div class="controls">
                            <select class="paytype" name="paytype" id="paytype">
                            <option value=""> All </option>
                            <option value="Y">Paid</option>
                            <option value="N">Unpaid</option>
                        </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="">
                        <input type="submit" class="btn btn-primary" name="search" id="btnsearch" value="Search">
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
            <a href="#"><img src="resources/images/excel.png" class="excel-icon"></a>

            <div class="tablediv">
                <div class="well" style="padding-top: 0px; overflow: auto; width: 100%;">
                    <div class="#estyle#"><span id="divmsg"></span></div>
                    <div class="tableWrap">
                        <table class="table table-striped table-hover" id="example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>
                                        <div class="checkbox"><input type="checkbox" id="selectall" class="selectall"></div>
                                    </th>
                                    <th>Activity Name</th>
                                    <th>Class</th>
                                    <th>Divison</th>
                                    <th>Contact No</th>
                                    <th>Email</th>
                                    <th>Roll No</th>
                                    <th>Amout</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th>View Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i=1;
                                    while($row = $report_data->fetch_assoc()){
                                     ?>
                                <tr>
                                    <td><?= $i; ?></td>
                                    <td>
                                        <div class="checkbox"><input type="checkbox" class="checkboxId" id="checkbox_<?= $row['RESERVATION_ID']; ?>" value="<?= $row['RESERVATION_ID']; ?>"></div>
                                    </td>
                                    <td><?= $row['ACTIVITY_TYPE'];?></td>
                                    <td><?= $row['NAME'];?></td>
                                    <td><?= $row['DIVISION'];?></td>
                                    <td><?= $row['MOBILE'];?></td>
                                    <td><?= $row['EMAIL'];?></td>
                                    <td><?= $row['ROLL_NUMBER'];?></td>
                                    <td><?= $row['AMOUNT']; ?></td>
                                    <td><?php if($row['PAYMENT_STATUS'] == 'Y'){echo "paid";} else {echo "unpaid";}?></td>
                                    <td><a target="_blank" href="pdf.php?activity_reg_id=<?= $row['RESERVATION_ID']; ?>"><img src="resources/images/pdf_icon.png" data-toggle="tooltip" data-placement="top" title="Download receipt as pdf"></a></td>
                                    <td><a href="javascript:void(0)" onClick="get_details('<?= $row['RESERVATION_ID'];?>')" data-toggle="modal" data-target="#details" class="btn btn-primary">Details</a></td>
                                </tr>
                                <?php 
                                    $i++;
                                }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <td><td><a href="javascript:void(0)" onClick="send_receipts()" class="btn btn-primary">Resend Receipt</a></td>
            <div class="pagination">
                <ul>
                    <?php
                     if($total_count['total'] % 2 == 0){
                        $total_pages = $total_count['total']/2;
                     } else {
                        $total_pages = floor($total_count['total']/2)+1; 
                     }?>
                     <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==1){echo 'javascript:void(0)';} else {echo 'activity_report.php?search='.$search.'&&page=1';}?>' class="<?php if(!isset($_GET['page']) || $_GET['page']==1){echo 'hidden';} ?>" id="first_page">First Page</a> </li>
                     <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==1){echo 'javascript:void(0)';} else if(!isset($_GET['page'])){echo 'javascript:void(0)';} else { echo 'activity_report.php?search='.$search.'&&page='.($_GET['page']-1);}?>' class="<?php if(!isset($_GET['page']) || $_GET['page']==1){echo 'hidden';} ?>">Prev Page</a> </li>
                    <?php for($i=1;$i<=$total_pages;$i++){
                        ?>
                        <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==$i){echo 'javascript:void(0)';} else if(!isset($_GET['page']) && $i==1){echo 'javascript:void(0)';} else { echo 'activity_report.php?search='.$search.'&&page='.$i;}?>' style ="<?php if(isset($_GET['page']) && $_GET['page']==$i){echo 'color:#000;';} else if(!isset($_GET['page']) && $i==1){echo 'color:#000;';} ?> ?>"><?php echo $i; ?></a></li>
                     <?php }?>
                     <li><a href='<?php if($total_pages<=1){echo 'javascript:void(0)';} else if(isset($_GET['page']) && $_GET['page']==($total_pages)){echo 'javascript:void(0)';} else if(!isset($_GET['page'])){echo 'activity_report.php?search='.$search.'&&page=2';} else { echo 'activity_report.php?search='.$search.'&&page='.($_GET['page']+1);}?>' class="<?php if((isset($_GET['page']) && $_GET['page']==$total_pages)||$total_pages==1){echo 'hidden';} ?>" >Next</a></li>
                     <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==($total_pages)){echo 'javascript:void(0)';} else {echo 'activity_report.php?search='.$search.'&&page='.$total_pages;}?>' class="<?php if((isset($_GET['page']) && $_GET['page']==$total_pages)||$total_pages == 1){echo 'hidden';} ?>" id="last_page">Last Page</a></li>
                </ul>
            </div>
            <?php echo $footer; ?>
        </div>
    </div>
</div>
<div class="modal fade" id="details" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Activity Details</h4>
            </div>
            <div class="modal-body actbox">
                <ul class="actdetails">
                    <li>
                        <div class="tag">First Name : </div><span id="fname"></span></li>
                    <li>
                        <div class="tag">Last Name : </div><span id="lname"></span></li>
                    <li>
                        <div class="tag">Roll No : </div><span id="roll_no"></span></li>
                </ul>
                <div class="tablediv">
                    <div class="#estyle#"><span id="divmsg"></span></div>
                    <h4>Activity</h4>
                    <div class="tableWrap">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Activity Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="activity">
                                <tr>
                                    <td>1</td>
                                    <td>Swimming</td>
                                    <td>500</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="textinput">Activity Amount</label>
                    <div class="controls">
                        <input id="total" name="total" placeholder="" class="input-xlarge" type="text"  readonly="readonly" value="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="textinput">Amount Paid</label>
                    <div class="controls">
                        <input id="total_amount" name="total_amount" placeholder="" class="input-xlarge" type="text"  readonly="readonly" value="">
                    </div>
                </div>
            </div>
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

    $('#selectall').click(function(){
        if ($('#selectall').is(':checked')){
            $('#resend_all').removeClass('hidden');
            $(".checkboxId").prop('checked', true)
        } else {
            $('#resend_all').addClass('hidden');
            $(".checkboxId").prop('checked', false)
        }
    })

    function get_details(registeration_id){
        $.ajax({
                url: "activity_report.php",
                type: "POST",
                data: {get_details_id : registeration_id},
                dataType: "json",
                success: function(data){
                    if(data!= ""){
                        $('#fname').empty().html(data.user_details['FIRST_NAME']);
                        $('#lname').empty().html(data.user_details['LAST_NAME']);
                        $('#roll_no').empty().html(data.user_details['ROLL_NUMBER']);
                        $('#activity').empty();
                        var total = 0
                        $.each(data.activity_details,function(index,value){
                            var tr_details = "<tr><td>"+(parseInt(index)+1)+"</td><td>"+value.ACTIVITY_NAME+"</td><td>"+value.FEES+"</td></tr>";
                            $('#activity').append(tr_details);
                            total = total + parseInt(value.FEES);
                        });
                        $('#total').val(total);
                        $('#total_amount').val(data.user_details['AMOUNT']);
                    }
                }
             });
    }

    function send_receipts(registeration_id){
        var checked_reservation = new Array();
        $('.checkboxId').each(function(){
            if($(this).is(':checked')){
                checked_reservation.push($(this).val());
            }
        });
        if(checked_reservation.length){
            $.ajax({
                url: "activity_report.php",
                type: "POST",
                data: {'send_mail':1,'registerations':checked_reservation},
                dataType: "json",
                success: function(data){
                    if(data!= ""){
                        $('#fname').empty().html(data.user_details['FIRST_NAME']);
                        $('#lname').empty().html(data.user_details['LAST_NAME']);
                        $('#roll_no').empty().html(data.user_details['ROLL_NUMBER']);
                        $('#activity').empty();
                        var total = 0
                        $.each(data.activity_details,function(index,value){
                            var tr_details = "<tr><td>"+(parseInt(index)+1)+"</td><td>"+value.ACTIVITY_NAME+"</td><td>"+value.FEES+"</td></tr>";
                            $('#activity').append(tr_details);
                            total = total + parseInt(value.FEES);
                        });
                        $('#total').val(total);
                        $('#total_amount').val(data.user_details['AMOUNT']);
                    }
                }
             });
        }else{
            alert("Please select a checkbox");
        }
    }
</script>
</body>

</html>