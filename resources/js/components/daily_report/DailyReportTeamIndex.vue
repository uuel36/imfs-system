<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
<template>
  <body :class="{ 'color-inverted': colorInversion}">
  <div @keyup="handleHotkeys" >
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fas fa-universal-access"></i> Accessibility Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      

        <p>Use the following hotkeys to control accessibility features:</p>
          <ul>
            <li>
              <strong>I</strong> - Toggle color inversion
              <p>Change the color scheme of the page for better visibility. Inverted colors can reduce eye strain and enhance readability.</p>
            </li>
            <li>
              <strong>T</strong> - Toggle large text
              <p>Enlarges the text size for improved readability. Large text is helpful for users with visual impairments or those who prefer larger fonts.</p>
            </li>
            <li>
              <strong>S</strong> - Toggle controls
              <p>Show/hide the accessibility controls menu. This menu provides quick access to the above features without the need to use hotkeys.</p>
            </li>
          </ul>
          <p><strong>Shortcut Keys:</strong></p>
          <ul>
            <li><strong>F</strong> - Toggle Full Screen</li>
            <li><strong>Esc</strong> - Exit Full Screen</li>
            <li><strong>Alt + h </strong> - Home </li>
          </ul>
          <p><strong>Navigation Keys:</strong></p>
          <ul>
            <li><strong>&uarr;</strong> - Arrow Up</li>
            <li><strong>&darr;</strong> - Arrow Down</li>
            <li><strong>&larr;</strong> - Arrow Left</li>
            <li><strong>&rarr;</strong> - Arrow Right</li>
            <li><strong>Tab</strong> - Move to the next focusable element</li>
          </ul>
          <p><strong>Note:</strong> Hotkeys and navigation keys may behave differently depending on your browser and operating system. If a hotkey conflicts with other keyboard shortcuts, you may need to adjust your browser settings.</p>
          <p><strong>Browser-specific Note:</strong></p>
          <ul>
            <li><strong>Chrome:</strong> Use hotkeys as described above.</li>
            <li><strong>Firefox:</strong> Use the hotkeys while holding down the Shift key.</li>
            <li><strong>Safari:</strong> Use the hotkeys while holding down the Option key.</li>
            <li><strong>Edge:</strong> Use the hotkeys while holding down the Alt key.</li>
          </ul>
        </div>
      <div class="modal-footer">
       
      </div>
    </div>
  </div>
</div>
    <!-- Skip button -->
    <button class="skip-button" @click="toggleControls">
        <i
          class="fas"
          :class="showControls ? 'fa-duotone fa-grip-lines-vertical custom-icon-blue' : 'fa-universal-access'"
        ></i>
      </button>
    
      <nav class="sticky-social-bar accessibility-controls" :class="{ 'show-controls': showControls }">
      <ul role="group" aria-label="Accessibility Controls">
        <li>
          <button
            class="control-button"
            :class="{ active: colorInversion }"
            @click="colorInversion = !colorInversion"
            :aria-pressed="colorInversion"
          >
            <i class="fas fa-adjust"></i> <!-- Font Awesome icon for "adjust" -->
          </button>
        </li>
        <li>
          <button
            class="control-button"
            :class="{ active: largeText }"
            @click="largeText = !largeText"
            :aria-pressed="largeText"
          >
            <i class="fas fa-font"></i> <!-- Font Awesome icon for "font" -->
          </button>
        </li>
        <li>
          <button type="button" class="btn btn-primary control-button" data-toggle="modal" data-target="#exampleModalCenter">
            <i class="fas fa-universal-access"></i>
</button>
        </li>
      </ul>
    </nav>
     <!-- Include your sidebar using Blade's @include directive and pass colorInversion -->
  
    <div class="content-wrapper" :class="{ 'color-inverted': colorInversion, 'large-text': largeText, }">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
                <h1 :class="{ 'color-inverted': colorInversion}" class="page_title">MANAGE DAILY REPORT TEAM</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="/home">Home</a></li>
                  <li class="breadcrumb-item active">Daily Report Team</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
  
          <!-- Main content -->
          <section id="sidebar" class="content" :class="{ 'color-inverted': colorInversion }">
            <div class="container-fluid">
                <daily-report-team-list></daily-report-team-list>
            </div>
          </section>
        </div>
      </div>
    </body>
  </template>
  
  <style>
  .skip-button {
    position: fixed;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    z-index: 1001;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 10px 0 0 10px;
    padding: 8px 12px;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s, right 0.3s;
  }
  
  .skip-button i {
    font-size: 20px;
  }
  /* Skip Button Position on Mobile */
  @media (max-width: 768px) {
    .skip-button {
      right: 20px; /* Adjust the value as needed */
    }
  }
  
  /* Sticky Social Bar Position on Mobile */
  @media (max-width: 768px) {
    .sticky-social-bar {
      position: fixed;
      top: 50%;
      right: 0;
      transform: translateY(-50%);
      width: auto; /* Adjust as needed */
      padding: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }
  }
  
  /* .skip-button:hover {
    background-color: #0056b3;
  } */
   .skip-button.moved {
    right: 60px;
    background-color: transparent;
  } 
  .custom-icon-blue {
    color: rgb(12, 127, 235) !important; /* Set the icon color to blue */
  }
  .custom-notification .notification-text {
    font-weight: bold;
  }
  
  /* Sticky Social Bar Styling */
  .sticky-social-bar {
    position: fixed;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1000;
  }
  .notification i.fa-duotone.fa-grip-lines-vertical {
    font-size: 36px; /* Adjust the size as needed */
  }
  .sticky-social-bar ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .control-button {
    display: block;
    margin-bottom: 10px;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    background-color: #f5f5f5;
    color: #333;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
  }
  
  .custom-icon-blue {
    color: #007bff !important; /* Set the icon color to blue */
  }
  .control-button.active {
    background-color: #007bff;
    color: #fff;
  }
  
  /* Improved Focus Styles */
  .control-button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.4);
  }
  .accessibility-controls {
    /* Your existing styles */
    margin-right: -320px;
    transition: margin-right 0.3s;
  }
  
  .show-controls {
    margin-right: 0;
  }
  .color-inverted 
  {
    filter: invert(100%);
  }
  
  .color-inverted .page_title {
    color: #fff !important;
  }
  .large-text {
    font-size: 1.2rem !important;
  }
  
  .large-text .el-table__body .el-table__row,
  .large-text .el-input,
  .large-text .el-date-picker,
  .large-text .el-table-column,
  .large-text .el-form-item label,
  .large-text .el-table .el-table__header th .cell,
  .large-text .el-table .el-table__footer td .cell 
  .large-text .el-button 
  .large-text .button
  .large-text .global-pagination
  .large-text .el-select{
  font-size: 1.2rem;
  }
  
  
  .adjusted-background {
    background-color: #f0f0f0;
  }
  </style>
  
  <script>
  export default {
    name: 'DailyReportTeamIndex',
    data() {
    return {
      colorInversion: false,
      largeText: false,
      showControls: false,
    };
  },
  watch: {
      colorInversion(newValue) {
          this.updateElementsClass(newValue);
      },
  },
  methods: {
    handleHotkeys(event) {
  // Check if the event target is not an input field to prevent conflicts
  if (event.target.tagName !== 'INPUT') {
    if (event.key === 'i') { // Press 'c' key for color inversion
      this.toggleColorInversion();
    } else if (event.key === 't') { // Press 't' key for toggling large text
      this.toggleLargeText();
    } else if (event.key === 'b') { // Press 'b' key for background adjustment
      this.toggleBackgroundAdjustment();
    } else if (event.key === 's') { // Press 's' key for toggling controls
      this.toggleControls();
    } else if (event.key === 'h') { // Press 'h' key to navigate to Home
      this.navigateToHome();
    }
  }
},
navigateToHome() {
  window.location.href = '/home';
},
  toggleColorInversion() {
    this.colorInversion = !this.colorInversion;
    this.updateElementsClass(this.colorInversion);
  },
  toggleLargeText() {
    this.largeText = !this.largeText;
  },
  toggleBackgroundAdjustment() {
    this.backgroundAdjustment = !this.backgroundAdjustment;
  },
    toggleControls() {
    this.showControls = !this.showControls;

    if (!this.showControls) {
      setTimeout(() => {
        this.moveButton();
      }, 300);
    } else {
      this.moveButton();
    }

    if (this.showControls) {
      this.$notify({
        title: 'User Accessibility Mode',
        // message: 'User Accessibility Mode Active',
        iconClass: 'fas fa-universal-access custom-icon-blue', // Add a custom class for the icon
        customClass: 'custom-notification'
      });
    }
  },
    moveButton() {
      const button = document.querySelector('.skip-button');
      if (button) {
        button.classList.toggle('moved');
      }
    },
  
      updateElementsClass(value) {
          const nav = document.querySelector('.main-header');
          const sidebar = document.getElementById('sidebar');
          
          if (nav && sidebar) {
              if (value) {
                  nav.classList.add('color-inverted');
                  sidebar.classList.add('color-inverted');
              } else {
                  nav.classList.remove('color-inverted');
                  sidebar.classList.remove('color-inverted');
              }
          }
      },
      handleHotkey(event) {
      if (event.key === 'a' && event.ctrlKey) { // Change 'a' to your desired key
        this.toggleControls();
      }
    },
  },
  
  created() {
    this.updateElementsClass(this.colorInversion);
    window.addEventListener('keydown', this.handleHotkey);
    // Check user's saved preferences and update the data properties accordingly
    // You can use localStorage or Vuex for persistent settings
  },
  beforeDestroy() {
    // Clean up the event listener when the component is destroyed
    window.removeEventListener('keydown', this.handleHotkey);
  },
};
</script>