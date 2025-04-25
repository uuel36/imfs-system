<aside  id="sidebar" class="main-sidebar sidebar-dark-success elevation-4" :class="{ 'color-inverted': colorInversion }" >
    <!-- Brand Logo -->
    <a  class="brand-link">
    <img src="/img/logo.png" alt="AdminLTE Logo" class="brand-image " style="opacity: .8">
    <span class="brand-text font-weight-light">I F M S</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
        @if(auth()->user()->userType->name == 'Administrator')
            <!-- Administrator -->
            <img src="/img/adminlogo.png" class="img-circle elevation-2" alt="User Image"> 
        @elseif(auth()->user()->userType->name == 'Leadman')
            <!-- Leadman -->
            <img src="/img/Leadman.png" class="img-circle elevation-2" alt="User Image"> 
        @elseif(auth()->user()->userType->name == 'Human Resource Staff')
            <!-- Human Resource Staff -->
            <img src="/img/Human Resource Staff.png" class="img-circle elevation-2" alt="User Image"> 
        @elseif(auth()->user()->userType->name == 'Warehouse Staff')
            <!-- Warehouse Staff -->
            <img src="/img/warehouse.png" class="img-circle elevation-2" alt="User Image"> 
        @elseif(auth()->user()->userType->name == 'Finance Staff')
            <!-- Finance Staff -->
            <img src="/img/Finance.png" class="img-circle elevation-2" alt="User Image"> 
        @endif
    </div>
    <div class="info">
    <a id="sidebar" style="text-size: 20px;" href="#" class="d-block text-success">{{ auth()->user()->userType->name }}</a>
    <span style="text-size: 3px;" class="d-block text-light" >{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</span>
</div>
</div>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
            with font-awesome or any other icon font library -->
            <li class="nav-item" id="sidebar">
                <a href="/home" class="nav-link {{ Route::is('home') ? 'active' : '' }}">
                    <i  class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                    Dashboard
                    </p>
                </a>
            </li>
            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Human Resource Staff')
            <li class="nav-header">ATTENDANCE</li>
            
            <li class="nav-item" id="sidebar">
                <a href="/attendance#/attendance" class="nav-link {{ Route::is('attendance.index') ? 'active' : '' }}"  onclick="focusOnSection('')">
                    <i class="nav-icon fas fa-user-clock"></i>
                    <p>Attendance</p>
                </a>
            </li>
            @endif
            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Human Resource Staff')
            <li class="nav-item" id="sidebar">
                <a href="/overtime#/overtime-list" class="nav-link {{ Route::is('overtime.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-clock"></i>
                    <p>Overtime</p>
                </a>
            </li>
            <li class="nav-header" id="sidebar">EMPLOYEES</li>
            <li class="nav-item">
                <a href="/employees#/employees" class="nav-link {{ Route::is('employee.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                <i class="fas fa-users nav-icon"></i>
                <p id="sidebar">Employees</p>
                </a>
            </li>
            @endif
            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Finance Staff')
            <li class="nav-header" id="sidebar">FINANCE</li>
            <li class="nav-item  {{ Route::is('payroll.index') ? 'menu-is-opening menu-open' : '' }}
            {{ Route::is('company.jobs') ? 'menu-is-opening menu-open' : '' }}"  onclick="focusOnSection('sectionId')">
                <a href="#" class="nav-link {{ Route::is('payroll.index') ? 'active' : '' }} {{ Route::is('company.jobs') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                <i class="nav-icon fas fa-address-card"></i>
                <p>
                    Payroll
                    <i class="fas fa-angle-left right"></i>
                </p>
                </a>
                <ul class="nav nav-treeview" id="sidebar">
                    <li class="nav-item">
                        <a href="/payroll#/generate" class="nav-link">
                        <i class="far fa-copy nav-icon"></i>
                        <p>Generate</p>
                        </a>
                    </li>
                    <li class="nav-item" id="sidebar">
                        <a href="/payroll#/payrolls" class="nav-link">
                        <i class="far fa-copy nav-icon"></i>
                        <p>Payroll</p>
                        </a>
                    </li>
                    <li class="nav-item" id="sidebar">
                        <a href="/payroll#/payroll-summary" class="nav-link">
                        <i class="far fa-copy nav-icon"></i>
                        <p>Summary</p>
                        </a>
                    </li>
                    <li class="nav-item" id="sidebar">
                        <a href="/payroll#/payroll-overall" class="nav-link">
                        <i class="far fa-copy nav-icon"></i>
                        <p>Overall Payrolls</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff')
            <li class="nav-header" id="sidebar">WAREHOUSE</li>
            <li class="nav-item">
                <a href="/stocks#/stocks" class="nav-link {{ Route::is('warehouse.stock') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Stocks</p>
                </a>
            </li>
            <li class="nav-item" id="sidebar">
                <a href="/items#/items" class="nav-link {{ Route::is('warehouse.item') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-clipboard-list"></i>
                    <p>Items</p>
                </a>
            </li>
            <li class="nav-item" id="sidebar">
                <a href="/categories#/categories" class="nav-link {{ Route::is('warehouse.category') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-code-branch"></i>
                    <p>Category</p>
                </a>
            </li>
            <li class="nav-item" id="sidebar">
                <a href="/monthly-report#/monthly-report" class="nav-link {{ Route::is('warehouse.monthly_report') ? 'active' : '' }}"  onclick="focusOnSection('monthly-report')">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Monthly Report </p>
                </a>
            </li>
            @endif
            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff' || auth()->user()->userType->name == 'Leadman')
            <li class="nav-header" id="sidebar">OPERATIONS</li>
                @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Leadman')
                <li class="nav-item">
                    <a href="/deploy-employee#/deploy-employees" class="nav-link {{ Route::is('deploy-employee.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>Deploy Team</p>
                    </a>
                </li>
                @endif
                @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff' || auth()->user()->userType->name == 'Leadman')
                <li class="nav-item" id="sidebar">
                    <a href="/deploy#/deploy" class="nav-link {{ Route::is('deploy.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>View Deployed Stocks</p>
                    </a>
                </li>
                @endif
                @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Leadman')
            <li class="nav-item" id="sidebar">
                <a href="/request-order#/request-order" class="nav-link {{ Route::is('warehouse.request_order') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Request Items </p>
                </a>
            </li>
            <li class="nav-item" id="sidebar">
                <a href="/return-tool#/return-tool" class="nav-link {{ Route::is('warehouse.return_tool') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Return Items </p>
                </a>
            </li>
            @endif
            @endif
            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Leadman')
            <li class="nav-header" id="sidebar">PRODUCTION</li>
            <li class="nav-item">
                <a href="/team#/teams" class="nav-link {{ Route::is('team.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-user-friends"></i>
                    <p>Manage Team</p>
                </a>
            </li>
            <li class="nav-item" id="sidebar">
                <a href="/daily-operation#/operations" class="nav-link {{ Route::is('daily-operation.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-paper-plane"></i>
                    <p>Daily Operation</p>
                </a>
            </li>
            <li class="nav-item" id="sidebar">
                <a href="/daily-report#/daily-reports" class="nav-link {{ Route::is('daily-report.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-file"></i>
                    <p>Daily Report Team</p>
                </a>
            <!-- </li>
            <li class="nav-item" id="sidebar">
                <a href="/daily-report-team#/daily-reports-team" class="nav-link {{ Route::is('daily-report-team.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-file"></i>
                    <p>Daily Report Team</p>
                </a>
            </li> -->
            <li class="nav-item" id="sidebar">
                <a href="/task#/tasks" class="nav-link {{ Route::is('task.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-check-square"></i>
                    <p>Task</p>
                </a>
            </li>
            <li class="nav-item" id="sidebar">
                <a href="/harvest#/harvest" class="nav-link {{ Route::is('harvest.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-cart-arrow-down"></i>
                    <p>Harvest</p>
                </a>
            </li>
            <li class="nav-item" id="sidebar">
                <a href="/linear-regression#/linears" class="nav-link {{ Route::is('linear.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-database"></i>
                    <p>Yield Forecast</p>
                </a>
            </li>
            <li id="sidebar" class="nav-item  {{ Route::is('month.index') ? 'menu-is-opening menu-open' : '' }}
            {{ Route::is('company.jobs') ? 'menu-is-opening menu-open' : '' }}"  onclick="focusOnSection('sectionId')">
                <a href="#" class="nav-link {{ Route::is('month.index') ? 'active' : '' }} {{ Route::is('company.jobs') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                <i class="nav-icon fas fa-address-card"></i>
                <p>
                    Half Month Report
                    <i class="fas fa-angle-left right"></i>
                </p>
                </a>
                <ul class="nav nav-treeview" id="sidebar">
                <li class="nav-item">
                    <a href="/half-month#/reports" class="nav-link">
                    <i class="far fa-copy nav-icon"></i>
                    <p>Reports</p>
                    </a>
                </li>
                <li class="nav-item" id="sidebar">
                    <a href="/half-month#/hgenerate" class="nav-link">
                    <i class="far fa-copy nav-icon"></i>
                    <p>Generate</p>
                    </a>
                </li>
                </ul>
            </li>
            @endif
            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Human Resource Staff')
            <li class="nav-header" id="sidebar">QR CODE</li>
            {{-- <li class="nav-item">
                <a href="/qr-code#/codes" class="nav-link {{ Route::is('qr-code.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-qrcode"></i>
                    <p>QR Codes</p>
                </a>
            </li> --}}
            <li class="nav-item" id="sidebar">
                <a href="/scanner" target="_blank" class="nav-link">
                    <i class="nav-icon fas fa-qrcode"></i>
                    <p>Scanner</p>
                </a>
            </li>
            @endif
            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Leadman')
            <li class="nav-header" id="sidebar">Area</li>
            <li class="nav-item">
                <a href="/areas#/areas" class="nav-link {{ Route::is('area.index') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-map-marker-alt"></i>
                    <p>Areas</p>
                </a>
            </li>
            @endif

            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Finance Staff')
            <li class="nav-header" id="sidebar">SETTINGS</li>
            <li class="nav-item">
                <a href="/finance-setting" class="nav-link {{ Route::is('finance.setting') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-file"></i>
                    <p>Finance Setting</p>
                </a>
            </li>
        
            @endif
            @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Human Resource Staff')
            <li class="nav-item" id="sidebar">
                <a href="/manage-staff" class="nav-link {{ Route::is('staff.manage') ? 'active' : '' }}"  onclick="focusOnSection('sectionId')">
                    <i class="nav-icon fas fa-file"></i>
                    <p>Manage Staff</p>
                </a>
            </li>
            @endif
            <!-- <li class="nav-item">
                <a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();" class="nav-link">
                <i class="nav-icon fas fa-power-off"></i>
                <p>Logout</p>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
                </a>
            </li> -->
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<script>
  function focusOnSection(sectionId) {
    var section = document.getElementById(sectionId);
    if (section) {
      section.scrollIntoView({ behavior: 'smooth' });
    }
  }
</script>

