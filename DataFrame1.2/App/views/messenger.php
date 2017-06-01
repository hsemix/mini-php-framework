<?=$this->display('header.php')?>
  <!-- Left side column. contains the logo and sidebar -->
  <?=$this->display('aside.php')?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper nopad-section">
    <!-- Content Header (Page header) -->
   <!-- Main content -->
    <section class="content group nopad-section" style="margin:0 auto;padding:0;">
       
        <div class="col-lg-12 col-md-12 col-xs-12 text-left nopad-section" style="margin:0 auto;">
           <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 all-contacts">
                   
                   <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 active" onclick="$musawo.websitechat.read();">
                      <i class="fa fa-comments  hidden-sm hidden-lg hidden-md" aria-hidden="true"></i> <span  class="hidden-xs"> Messages </span> <sup class="label label-danger counter-container"><?=$chats->where('status', '0')->count()?></sup>
               </div>
                   <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                       <i class="fa fa-users  hidden-sm hidden-lg hidden-md" aria-hidden="true"></i>  <span  class="hidden-xs"> Contacts </span>
               </div>
                   <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                       <i class="fa fa-bell hidden-sm hidden-lg hidden-md" aria-hidden="true"></i>  <span class="hidden-xs"> Notifictions </span> <sup class="label label-danger">10</sup>
               </div>
        </div>               
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 nopad-section group messenger-chats">
             <div class="recent-messeges box-comments recent-container">
              
              <?php foreach($chats as $chat):?>
              <div class="box-comment" data-doc="<?=$chat->reciever->id?>" data-chat="<?=$chat->chat_id?>" onclick="$musawo.websitechat.showthechat(this);" style="cursor:pointer;">
                <!-- User image -->
                <img class="img-circle img-sm" src="/<?=$resource?>dist/img/user3-128x128.jpg" alt="User Image">

                <div class="comment-text">
                      <span class="username">
                        <?=$chat->sender->fullname?>
                        <span class="text-muted pull-right"><?=(new DateTime($chat->created_at, new DateTimeZone('Africa/Kampala')))->format('jS M Y ga')?></span>
                      </span><!-- /.username -->
                      <span class="chat-recent-text">
                      <?=$chat->content?>
                    </span> 
                    <span>
                      <b>To:</b> <?=$chat->reciever->fullname?> 
                      <?php if($chat->status == 0):?>
                        <i class="text-muted label-success new-messages  pull-right"> New </i>
                      <?php endif?>
                    </span>
                </div>
                <!-- /.comment-text -->
              </div>
              <?php endforeach?>
             

            </div> 
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 nopad-section current-message chatingChange ">
             <div class="direct-chat-current-messages-2 group chatTitle hidden-md hidden-lg">
                   <i class="fa fa-long-arrow-left backClosing"  aria-hidden="true" onclick="$musawo.websitechat.closechat();" ></i>     
                   <span class="selected-doc"> </span>
                   <i class="fa fa-times pull-right backClosing" aria-hidden="true" onclick="$musawo.websitechat.closechat();" ></i>
           </div>       
             <div class="direct-chat-current-messages chat-container">
                    <!-- Message. Default to the left -->
                    
                    <!-- /.direct-chat-msg -->

                    <!-- Message to the right -->
                    
                    
                    <!-- /.direct-chat-msg -->

                  </div>
                  <!--/.direct-chat-messages-->

        </div>
       
          <!-- /.box -->

      </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?=$this->display("footer.php")?>
<script type="text/javascript">
   
   $(function(){

    setTimeout(function(){
      $musawo.websitechat.check();
    }, loadTime);
   })

 </script>