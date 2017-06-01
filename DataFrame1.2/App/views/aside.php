<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      
      <!-- Sidebar Menu -->
      <ul class="sidebar-menu clearfix">
        <!-- Optionally, you can add icons to the links -->
        
        <li class="active"><a href="#"><i class="fa fa-newspaper-o" aria-hidden="true"></i> <span>News</span></a></li>
        <li><a href="/dashboard"><i class="fa fa-medkit" aria-hidden="true"></i> <span>First Aid</span></a></li>
        <li><a href="/messenger"><i class="fa fa-comments-o" aria-hidden="true"></i> <span>Messenger</span> <sup class="label label-danger label-new-messages pull-right"><?=$chats->where('status', '0')->count()?></sup></a></li>
        <li><a href="/dashboard"><i class="fa fa-map-marker" aria-hidden="true"></i> <span>Locations</span></a></li>
        <li><a href="/dashboard"><i class="fa fa-question-circle" aria-hidden="true"></i> <span>Forums</span></a></li>
        <li><a href="/dashboard"><i class="fa fa-heartbeat" aria-hidden="true"></i> <span>Symptoms</span></a></li>
        <li><a href="/dashboard"><i class="fa fa-th-list" aria-hidden="true"></i> <span>Specialists</span></a></li>
        <li><a href="/dashboard"><i class="fa fa-file" aria-hidden="true"></i> <span>Diet Guide</span></a></li>
        <li><a href="/dashboard"><i class="fa fa-calendar" aria-hidden="true"></i> <span>Appointments</span></a></li>
        <li class=""><a href="/dashboard"><i class="fa fa-clock-o" aria-hidden="true"></i> <span>Reminder</span></a></li>
        <li class=""><a href="/dashboard"><i class="fa fa-shopping-bag" aria-hidden="true"></i> <span>Store</span></a></li>
        <li><a href="/dashboard"><i class="fa fa-info-circle" aria-hidden="true"></i> <span>Feedback</span></a></li>
        <li class="treeview">
          <a href="/dashboard"><i class="fa fa-users" aria-hidden="true"></i> <span>Users</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="/dashboard"><i class="fa fa-user-md" aria-hidden="true"></i> Doctors</a></li>
            <li><a href="/dashboard"><i class="fa fa-user" aria-hidden="true"></i> Users</a></li>
          </ul>
        </li>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>