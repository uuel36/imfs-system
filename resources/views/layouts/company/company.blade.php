
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<link rel="shortcut icon" type="x-icon" href="/img/logo.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Farm Management System</title>
  <script src="{{ asset('/js/app.js') }}" defer></script>
  <!-- Scripts -->

  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{asset('vendors/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css'">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('vendors/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('vendors/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{asset('vendors/plugins/jqvmap/jqvmap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('vendors/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('vendors/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('vendors/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('vendors/plugins/summernote/summernote-bs4.min.css')}}">

    <script>
        window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token(),
            'locale' => config('app.locale'),
        ]); ?>;
    </script>


</head>
<body class="hold-transition sidebar-mini layout-fixed">


    <div class="wrapper">


  
</div>
      <!-- Preloader -->
      <!-- <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="/img/logo.png" alt="E Work Finder" height="80" width="60">
      </div> -->

      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
          @csrf
          @php
              $newDataCount = 0;
          @endphp

          @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff' || auth()->user()->userType->name == 'Leadman')
          <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
            <i class="far fa-bell"></i>
            <span class="badge badge-warning navbar-badge" id="notification-badge">{{ $newDataCount }}</span>
            </a>
          </li> 
          @endif
        </ul>
       <!-- Search input with icon -->
    <div class="input-container">
        <i class="fas fa-search input-icon"></i>
        <input class="form-control input-field" list="browsers" placeholder="Search">

    </div>
    <datalist id="browsers">
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Human Resource Staff')
  <option value="Attendance">Attendance/Employees Attedances</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Human Resource Staff')
  <option value="Overtime">Overtime/Time out</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Human Resource Staff')
  <option value="Employees">Employees/Add Employees/Position/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Finance Staff')
  <option value="Generate Payroll">Payroll/Generate Payroll/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Finance Staff')
  <option value="Payroll">Payroll/Generate Payroll/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Finance Staff')
  <option value="Summary Payroll">Summary Payroll/Add Payroll/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Finance Staff')
  <option value="Overall Payrolls">Manage Payroll/History/Payroll/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Finance Staff')
  <option value="Stocks">Over Payroll/History/Payroll/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff')
  <option value="Items">Stocks/Add stocks/Add stocks/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff')
  <option value="Monthly Report Warehouse ">items/Add stocks/Add stocks/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff')
  <option value="Return Tools">Monthy Report/Monthy Report Items/items/Reports/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="Deploy Team">items/Tools/Return/Return Tools/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="View Deployed Stocks">Deploy/Teams/Area/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff')
  <option value="Manage Team">Deploy/Deploy Stocks/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadmn')
  <option value="Daily Operation">Daily/Operation/Daily Operation/Add Operation/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="Daily Report Employees">Daily/Reports/Daily Reports/Add Report/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="Daily Report Team">Daily/Reports/Daily Reports team/Team/Task/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="Task">Task/Add task/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="Harvest">Harvest/Add Harvest/Area/Stem Cuts/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="Yeild Forecast">Linear/Regression/Area/Overall Graph/Add Linear Regression/Graph/Yeild Forecast/Stem cuts/Forecasted Stem Cuts/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff')
  <option value="Linear Regression Details">Linear/Reggression/Area/Actual/Analyze/Graph/Accuracy/Stem cuts/Forecasted Stem Cuts/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="Half Month Report Yield Forecasted">Linear/Reggression/Area/Actual/Analyze/Graph/Accuracy/Stem cuts/Forecasted Stem Cuts/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="Generate Half Month Report Yield Forecasted">/Half/Generate/Reports/Half Month Report/Logistis/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Human Resource Staff')
  <option value="QR code for Attendace">Attendance/QR/Code/Scanner/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'leadman')
  <option value="Areas">Add Area/Area/Map/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Finance Staff')
  <option value="Finance Settings">Finance/Setting/Finance Settings/Ovetime/Position/SSS/Philhealth/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Human Resource Staff')
  <option value="Manage Staff">Staff/Setting/Staff Accounts/Add Account/Position/Account/Add Staff/</option>
  @endif
  @if(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff')
  <option value="">
@endif
</datalist>
        <!-- Right navbar links -->
        <div class="modal-container" id="modal-container">
    <div class="modal-content">
       
        <p>Are you sure you want to disapprove this request?</p>
        <div class="modal-buttons">
            <button class="btn btn-danger small-button" id="confirm-disapprove-button">Yes</button>
            <button class="btn btn-secondary small-button" id="close-modal-button">No</button>
        </div>
    </div>
</div>

<div class="modal-container" id="approve-modal-container">
    <div class="modal-content">
        <p>Are you sure you want to approve this request?</p>
        <div class="modal-buttons">
            <button class="btn btn-success small-button" id="confirm-approve-button">Yes</button>
            <button class="btn btn-secondary small-button" id="close-approve-modal-button">No</button>
        </div>
    </div>
</div>

<div class="modal-container" id="fail-modal-container">
    <div class="modal-content">
        <p>Failed to approve the request.</p>
        <div class="modal-buttons">
            <button class="btn btn-secondary small-button" id="close-fail-modal-button">Close</button>
        </div>
    </div>
</div>



        
        <ul class="navbar-nav ml-4">
    
          <li class="nav-item">
                <a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();" class="nav-link text-danger">
                <i class="nav-icon fas fa-power-off"></i>
                Logout
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none"> 
                    @csrf
                </form>
                
                </a> 
                
            </li>
   
        </ul>
      </nav>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      @include('layouts.company.component.sidebar')
      <div class="" id="app">
        @yield('content')
      </div>

      <!-- Content Wrapper. Contains page content -->

      <!-- /.content-wrapper -->
      <!-- <footer class="main-footer">
        <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
          <b>Version</b> 3.1.0
        </div>
      </footer>  -->

      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark" style="width: 400px; border: none; border-radius: 10px; overflow-y: auto; scrollbar-width: thin;">
    <div class="notification-header">
        <span>Notifications</span>
    </div>

    <div class="notification-content">
        <!-- Example Notification Item -->
        @php
            $requestOrders = $requestOrders->reverse(); // Reverse the order of the array
        @endphp


        @foreach($requestOrders as $requestOrder)
    @if(auth()->user()->userType->name == 'Leadman' && auth()->user()->id == $requestOrder->leadman_id)
        <div class="notification-item">
            @if($requestOrder->is_disapproved)
                <p style="color: #E37371 ;">Your request was disapproved by the warehouse.</p>
                {{ $itemNames[$requestOrder->item_id] ?? '' }}
                <p style="font-size: 13px;">{{ \Carbon\Carbon::parse($requestOrder->created_at)->diffForHumans() }}</p>
            @elseif(!$requestOrder->is_approved)
                <!-- Pending Approval or Disapproval -->
            @else
                <p><strong>Warehouse</strong> accepted your item request.</p>
                {{ $itemNames[$requestOrder->item_id] ?? '' }}
                <p style="font-size: 13px;">{{ \Carbon\Carbon::parse($requestOrder->created_at)->diffForHumans() }}</p>
            @endif
        </div>
    @elseif(auth()->user()->userType->name == 'Administrator' || auth()->user()->userType->name == 'Warehouse Staff')
        <div class="notification-item">
            <p style="font-size: smaller;">
                <div>
                    {{ $leadmanNames[$requestOrder->leadman_id] ?? '' }} requested an item {{ $itemNames[$requestOrder->item_id] ?? '' }} ({{ $requestOrder->quantity ?? '' }}) in the {{ $areaNames[$requestOrder->area_id] ?? '' }}.
                </div>
                <div>
                    <p style="font-size: 13px;">{{ \Carbon\Carbon::parse($requestOrder->created_at)->diffForHumans() }}</p>
                </div>
            </p>
            @if(!$requestOrder->is_approved && !$requestOrder->is_disapproved)
    @php
        $newDataCount++;
    @endphp
   <div class="button-container">
    @if($requestOrder->is_quantity == 1)
    <p style="color: #E37371;" >The request cannot be approved. The remaining quantity of the item {{ $itemNames[$requestOrder->item_id] ?? '' }} is 0.</p>
    <form action="{{ route('disapprove-request', $requestOrder->id) }}" method="post" class="disapprove-form">
            @csrf
            <button type="button" class="btn btn-danger disapprove-button" data-request-id="{{ $requestOrder->id }}">Disapprove</button>
        </form>
    @else
        <form action="{{ route('approve-request', $requestOrder->id) }}" method="post" class="approve-form">
            @csrf
            <button type="button" class="btn btn-success approve-button" data-request-id="{{ $requestOrder->id }}">Approve</button>
        </form>

        <form action="{{ route('disapprove-request', $requestOrder->id) }}" method="post" class="disapprove-form">
            @csrf
            <button type="button" class="btn btn-danger disapprove-button" data-request-id="{{ $requestOrder->id }}">Disapprove</button>
        </form>
    @endif
</div>

@elseif($requestOrder->is_disapproved)
    <p style="color: #E37371;">Request canceled</p>
@else
    <p>Request approved by: Warehouse ✅ </p>
@endif


        </div>
    @endif
@endforeach

    </div>
</aside>

      <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <script>
    function openModal() {
        document.getElementById('modal-container').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('modal-container').style.display = 'none';
    }

    function disapproveRequest() {
        const button = document.querySelector('.disapprove-button[disabled]');
        if (button) return;

        const requestId = button.getAttribute('data-request-id');
        const modal = document.getElementById('modal-container');

        button.setAttribute('disabled', 'true');
        button.innerHTML = 'Loading...';

        fetch(`/disapprove-request/${requestId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                button.removeAttribute('disabled');
                button.innerHTML = 'Disapprove';
                alert('Failed to disapprove the request. Please try again.');
            }
            closeModal(); // Close the modal after the action is complete
        });
    }
</script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notificationBadge = document.getElementById('notification-badge');
            notificationBadge.innerText = '{{ $newDataCount }}';
        });
      </script>

      <script>
        document.addEventListener('DOMContentLoaded', function () {
    const approveButtons = document.querySelectorAll('.approve-button');
    const approveModal = document.getElementById('approve-modal-container');
    const closeApproveModalButton = document.getElementById('close-approve-modal-button');
    const confirmApproveButton = document.getElementById('confirm-approve-button');
    const failModal = document.getElementById('fail-modal-container');
    const closeFailModalButton = document.getElementById('close-fail-modal-button');
    let currentRequestId = null;

    approveButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            currentRequestId = this.getAttribute('data-request-id');
            approveModal.style.display = 'block';
        });
    });

    closeApproveModalButton.addEventListener('click', function() {
        approveModal.style.display = 'none';
    });

    closeFailModalButton.addEventListener('click', function() {
        failModal.style.display = 'none';
    });

    confirmApproveButton.addEventListener('click', function() {
        if (!currentRequestId) return;

        const approveButton = document.querySelector('.approve-button[data-request-id="' + currentRequestId + '"]');
        if (!approveButton) return;

        approveButton.setAttribute('disabled', 'true');
        approveButton.innerHTML = 'Loading...';

        fetch(`/approve-request/${currentRequestId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                approveButton.removeAttribute('disabled');
                approveButton.innerHTML = 'Approve';
                failModal.style.display = 'block';
            }
            approveModal.style.display = 'none';
        });
    });
});

</script>

<script>
    // Assuming you have jQuery included in your project

    $(document).ready(function() {
        $('#close-fail-modal-button').on('click', function() {
            $('#fail-modal-container').hide();
        });
    });

    // Function to handle the response from the backend
    function handleFailureResponse(response) {
        if (response.error) {
            $('#fail-message').text(response.error);
            if (response.stock) {
                $('#stock-id').text('Stock ID: ' + response.stock.id);
                $('#stock-quantity').text('Stock Quantity: ' + response.stock.quantity);
                $('#stock-info').show();
            } else {
                $('#stock-info').hide();
            }
            $('#fail-modal-container').show();
        }
    }
</script>
  <script>
document.addEventListener('DOMContentLoaded', function () {
   
    const disapproveButtons = document.querySelectorAll('.disapprove-button');
    const modal = document.getElementById('modal-container');
    const closeModalButton = document.getElementById('close-modal-button');
    const confirmDisapproveButton = document.getElementById('confirm-disapprove-button');
    let currentRequestId = null;



    disapproveButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            const requestId = this.getAttribute('data-request-id');
            currentRequestId = requestId;

            modal.style.display = 'block';
        });
    });

    closeModalButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    confirmDisapproveButton.addEventListener('click', function() {
        if (!currentRequestId) return;

        const disapproveButton = document.querySelector('.disapprove-button[data-request-id="' + currentRequestId + '"]');
        if (!disapproveButton) return;

        disapproveButton.setAttribute('disabled', 'true');
        disapproveButton.innerHTML = 'Loading...';

        fetch(`/disapprove-request/${currentRequestId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                disapproveButton.removeAttribute('disabled');
                disapproveButton.innerHTML = 'Disapprove';
                alert('Failed to disapprove the request. Please try again.');
            }
            modal.style.display = 'none';
        });
    });
});
</script>


<script>
    document.querySelector('input.form-control').addEventListener('input', function () {
      var selectedValue = this.value;
      // Define the URLs associated with each option
      var optionUrls = {
        'Attendance': '/attendance#/attendance',
        'Overtime': '/overtime#/overtime-list',
        'Employees': '/employees#/employees',
        'Generate Payroll': '/payroll#/generate',
        'Payroll': '/payroll#/payrolls',
        'Summary Payroll': '/payroll#/payrolls',
        'Overall Payrolls': '/payroll#/payroll-overall',
        'Stocks': '/stocks#/stocks',
        'Items': '/items#/items',
        'Monthly Report Warehouse': '/monthly-report#/monthly-report',
        'Return Tools': '/return-tool#/return-tool',
        'Deploy Team': '/deploy-employee#/deploy-employees',
        'View Deployed Stocks': '/deploy#/deploy',
        'Manage Team': '/team#/teams',
        'Daily Operation': '/daily-operation#/operations',
        'Daily Report Employees': '/daily-report#/daily-reports',
        'Daily Report Team': '/daily-report-team#/daily-reports-team',
        'Task': '/task#/tasks',
        'Harvest': '/harvest#/harvest',
        'Yeild Forecast': '/linear-regression#/linears',
        'Linear Regression Details': '/linear-regression#/details',
        'Half Month Report Yield Forecasted': '/half-month#/reports',
        'Generate Half Month Report Yield Forecasted': '/half-month#/hgenerate',
  
        'Areas': '/areas#/areas',
        'Finance Settings': '/finance-setting#/',
        'Manage Staff': '/manage-staff#/',
        // Add URLs for other options here
      };

      // Check if the selected value has a corresponding URL
      if (optionUrls[selectedValue]) {
        // Redirect to the URL in the current tab for other options
        window.location.href = optionUrls[selectedValue];
      } else if (selectedValue === 'QR code for Attendace') {
        // Open a new tab with the specified URL for the QR code option
        var qrCodeUrl = '/scanner#/';
        var newTab = window.open(qrCodeUrl, '_blank');
        if (newTab) {
          // Focus the newly opened tab (if it was allowed)
          newTab.focus();
        } else {
          // Inform the user that the pop-up was blocked
          alert('Pop-up blocked. Please allow pop-ups for this website.');
        }
      }
    });
  </script>
  
    <!-- jQuery -->
    <script src="{{ asset('vendors/plugins/jquery/jquery.min.js')}}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('vendors/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <!-- Bootstrap 4 -->
    <!-- Tempusdominus Bootstrap 4 -->
    <!-- Summernote -->
    <script src="{{ asset('vendors/plugins/summernote/summernote-bs4.min.js')}}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('vendors/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('vendors/dist/js/adminlte.js')}}"></script>
    <!-- AdminLTE for demo purposes -->
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    {{--<script src="{{ asset('vendors/dist/js/pages/dashboard.js')}}"></script> --}}
    </body>
</html>
<style>

.button-container {
    display: flex;
    gap: 10px; /* Adjust gap as needed */
}

/* Optional: Style the forms to have no margins */
.approve-form,
.disapprove-form {
    margin: 0;
}

/* Optional: Style the buttons if needed */
.btn {
    padding: 5px 10px; /* Adjust padding */
    font-size: 14px; /* Adjust font size */
}
.modal-container {
    display: none;
    position: fixed;
    z-index: 1000;
    right: 500px;
    top: 0;
    width: 35%;
    height: 100%;
    overflow: auto;
   
}
.modal-buttons {
    display: flex;
    justify-content: flex-end; /* Align buttons to the right */
    gap: 10px; /* Space between buttons */
}

/* Small Button */
.small-button {
    padding: 5px 10px; /* Smaller padding for smaller button */
    font-size: 14px; /* Smaller font size */
}

/* Modal Content */
.modal-content {
    background-color: #E6E0E0;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    border-radius: 10px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    animation-name: modal-animation;
    animation-duration: 0.4s;
  
}

/* Modal Animation */
@keyframes modal-animation {
    from {top: -300px; opacity: 0;}
    to {top: 0; opacity: 1;}
}

/* Close Button */
.close-button {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close-button:hover,
.close-button:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Adjust the position and style of the search icon */
.input-icon {
    position: absolute;
    top: 50%;
    left: 10px; /* Adjust the left position based on your design */
    transform: translateY(-50%);
    z-index: 2; /* Ensure the icon is above the input field */
    color: #aaa; /* Adjust the color as needed */
}

/* Style the input field container */
.input-container {
    position: relative;
    width: 200px; /* Adjust the width as needed */
    margin: 0 auto; /* Center the input horizontally */
}

/* Style the input field */
.input-field {
    width: 250px;
    padding-left: 30px; /* Adjust the padding based on the icon width */
    border-radius: 15px; /* Adjust the border radius as needed */
    border: 1px solid #aaa; /* Add a solid border with the color of your choice */
}

/* Adjust the width of the container if needed */

/* Notification Box Styles */
      .notification-box {
            display: none;
            position: fixed;
            top: 120px; /* Adjust the top position based on your design */
            right: 15px;
            width: 350px;
            background-color: #C0C0C0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            z-index: 1000;
        }

        .notification-header {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-content {
            padding: 3px;
        }

        .notification-item {
            padding: 4px;
            border-bottom: 1px solid #ddd;
        }

        .close-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #333;
        }
         /* Modern scrollbar for WebKit browsers (Chrome, Safari, newer versions of Edge) */
         ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
        }

        /* Modern scrollbar for Firefox */
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
  </style>