<?php $this->display("header.php")?>
  <!-- Left side column. contains the logo and sidebar -->
  <?=$this->display('aside.php')?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
   <!-- Main content -->
    <section class="content group nopad-section" >
       
        <div class="col-lg-9 col-md-8 col-xs-12 text-left nopad-section" style="margin:0 auto;">
                          
          
      <div class="col-md-12">
          <!-- Custom Tabs (Pulled to the right) -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right col-xs-12 col-sm-12" >
              <li class="active"><a href="#tab_1-1" data-toggle="tab">Appointments</a> <span class="label label-danger label-dangerss"><?=$appointments->where('status', '0')->count()?></span> </li>
              <li><a href="#tab_2-2" data-toggle="tab">Users</a> <span class="label label-danger label-dangerss"><?=$users->count()?></span></li>
              <li><a href="#tab_3-2" data-toggle="tab">Feedback</a> <span class="label label-danger label-dangerss"><?=$feedback->count()?></span> </li>
              <li class="pull-left header hidden-xs hidden-sm" ><i class="fa fa-th"></i><span >Latest Feeds</span></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active nopad-section" id="tab_1-1">
                
                           
          <div class="box box-info clearfix">
            <div class="box-header with-border">
              <h3 class="box-title">Appointments</h3>

            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>App ID</th>
                    <th>Appointment</th>
                    <th>Status</th>
                    <th>Patient</th>
                    <th>Options</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($appointments as $appointment):?>
                  <tr>
                    <td><a href="pages/examples/invoice.html"><?=$appointment->id?></a></td>
                    <td><?=$appointment->title?></td>
                    <?php if($appointment->status == '1'):?>
                    <td><span class="label action label-success">Confirmed</span></td>
                    <?php else:?>
                    <td><span class="label action label-warning">Pending</span></td>
                    <?php endif?>
                    <td>
                      <div class="sparkbar" data-color="#00a65a" data-height="20"><?=$appointment->user->fullname?></div>
                    </td>
                    <td>
                      <a href="javascript:void(0)" onclick="$musawo.appointment.confirm(this);" data-apId="<?=$appointment->id?>"><i class="fa fa-check" aria-hidden="true"></i></a>
                      <a href="#"><i class="fa fa-clock-o" aria-hidden="true"></i></a>
                    </td>
                  </tr>
                <?php endforeach?>
                 
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
            </div>
            <!-- /.box-footer -->


              <!-- /.table-responsive -->
            </div>


              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane nopad-section" id="tab_2-2">
                   
                    <div class="box box-danger">
                          <div class="box-header with-border">
                            <h3 class="box-title ">Members</h3>
                            <div class="box-tools pull-right">
                              <span class="label label-danger">8 New Members</span>
                            </div>
                          </div>
                          <!-- /.box-header -->
                          <div class="box-body no-padding">
                            <ul class="users-list clearfix">
                            <?php foreach($users as $user):?>
                              <li>
                                <img src="/<?=$resource?>dist/img/user1-128x128.jpg" alt="User Image">
                                <a class="users-list-name" href="#"><?=$user->fullname?></a>
                                <span class="users-list-date"><?=$user->created_at?></span>
                              </li>
                            <?php endforeach?>
                              
                            </ul>
                            <!-- /.users-list -->
                          </div>
                          <!-- /.box-body -->
                          <div class="box-footer text-center">
                            <a href="javascript:void(0)" class="uppercase">View All Users</a>
                          </div>
                <!-- /.box-footer -->
              </div>

              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane nopad-section" id="tab_3-2">
                        
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Recently Feedback</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <ul class="products-list product-list-in-box">
              <?php foreach($feedback as $feed):?>
                <li class="item">
                  <div class="product-img">
                    <img src="/<?=$resource?>dist/img/default-50x50.gif" alt="Product Image">
                  </div>
                  <div class="product-info">
                    <a href="javascript:void(0)" class="product-title"><?=$feed->user->fullname?>-<?=$feed->inquiry?>
                      <!--span class="label label-warning pull-right">$1800</span--></a>
                        <span class="product-description">
                          <?=$feed->description?>
                        </span>
                  </div>
                </li>
              <?php endforeach?>
                <!-- /.item -->
              </ul>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
              <a href="javascript:void(0)" class="uppercase">View All Products</a>
            </div>
            <!-- /.box-footer -->
          </div>



              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
        </div>
          
        <div class="col-md-12" style="margin:0 auto;" >
              <!-- USERS LIST -->
              <div class="box box-danger">
                <div class="box-header with-border">
                  <h3 class="box-title">Doctors</h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                  <ul class="users-list clearfix">
                  <?php foreach($users->where('type_id', '2') as $user):?>
                    <li>
                      <img src="/<?=$resource?>dist/img/user1-128x128.jpg" alt="User Image">
                      <a class="users-list-name" href="#"><?=$user->fullname?></a>
                      <span class="users-list-date"><?=$user->created_at?></span>
                    </li>
                   <?php endforeach?>
                    
                  </ul>
                  <!-- /.users-list -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                  <a href="javascript:void(0)" class="uppercase">View All Users</a>
                </div>
                <!-- /.box-footer -->
              </div>
              <!--/.box -->
            </div>

       






  <div class="col-md-12" style="margin:0 auto;" >
                 <!-- TABLE: LATEST ORDERS -->
          </div>
          <!-- /.box -->

      </div>
                <div class="col-lg-3 col-md-4 col-xs-12 text-left">

       </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?=$this->display("footer.php")?>
