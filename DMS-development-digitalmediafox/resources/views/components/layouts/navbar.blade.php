<style>
.user-dropdown {
    position: relative;
    display: inline-block;
}

.user-btn {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
}

.user-btn-content {
    display: flex;
    align-items: center;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

.user-name {
    margin-left: 8px;
    font-weight: 500;
}

.user-menu {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 6px);
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    min-width: 150px;
    z-index: 1000;
}

.user-menu.show {
    display: block;
}

.user-menu a,
.user-menu button {
    display: block;
    padding: 8px 12px;
    color: #333;
    text-decoration: none;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
}

.user-menu a:hover,
.user-menu button:hover {
    background: #f5f5f5;
}

.goog-te-banner-frame.skiptranslate {
    display: none !important;
}

a.VIpgJd-ZVi9od-l4eHX-hSRGPd {
    display: none;
}
iframe{display:none;}

/* Prevent body from shifting down */
body {
    top: 0 !important;
}
#google_translate_element {
    padding: 0px 20px !important;

}
.goog-te-combo {
    padding: 5px !important;
    border: 1px solid black !important;
    border-radius: 5px !important;
    color:#722c81;
    
}
.goog-te-gadget {
    color: white !important;
}

#page-topbar.without-sidebar {
    transition: all 0.3s ease;
    left: 0 ;
}

#page-topbar.with-sidebar {
    left: var(--vz-vertical-menu-width);
     transition: all 0.3s ease;
}


.footer.without-sidebar {
    transition: all 0.3s ease;
    left: 0 ;
}
.footer{ transition: all 0.3s ease;}
.footer.with-sidebar {
    left: var(--vz-vertical-menu-width);
     transition: all 0.3s ease;
}

/* Small devices (phones landscape, ≥576px) */
@media (min-width: 360px) and (max-width: 999px) {
  #page-topbar {
        left: 0 !important;
    }
    .footer {
        left: 0 !important;
    }
}


</style>
<header id="page-topbar" class="with-sidebar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <button type="button" 
        class="px-3 btn btn-sm fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" 
        id="sidebarToggle" onclick="Res()">
    <span class="hamburger-icon">
        <span></span>
        <span></span>
        <span></span>
    </span>
</button>

            </div>
          <div>
              
          </div>

            <div class="d-flex align-items-center">
        <!-- Hidden Google Translate element -->
        <div id="google_translate_element"></div>

                        
            
        <div class="user-dropdown" id="userDropdown">
        <button type="button" class="user-btn" id="userDropdownBtn">
        <span class="user-btn-content">
        <img class="user-avatar"
        src="{{ Vite::asset('resources/assets/images/users/avatar-1.jpg') }}"
        alt="Header Avatar">
        <span class="user-name">{{ auth()->user()?->name ?? 'Guest' }}</span>
        </span>
        </button>
        
        <div class="user-menu" id="userDropdownMenu">
        <livewire:logout />
        </div>
        </div>
            
            </div>
        </div>
    </div>
    </header>


<script>
function Res(val) {
    // -------------------------
    // 1. Toggle sidebar classes
    // -------------------------
    const $topbar = $("#page-topbar");
    const $footer = $(".footer");


    if(val!=0){


         if ($topbar.hasClass("with-sidebar")) {
        $topbar.removeClass("with-sidebar").addClass("without-sidebar");
        $footer.addClass("without-sidebar");
    } else {
        $topbar.removeClass("without-sidebar").addClass("with-sidebar");
        $footer.removeClass("without-sidebar");
    }
    


   

    // --------------------------------
    // 2. Initialize user dropdown menu
    // --------------------------------
    const btn = document.getElementById('userDropdownBtn');
    const menu = document.getElementById('userDropdownMenu');
    if (!btn || !menu) return;

    // Remove any existing click listeners
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);

    // Add click listener to toggle dropdown
    newBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.toggle('show');
    });

    // Remove old global listener to avoid duplicates
    const oldDocListener = document.getElementById('dropdownDocListener');
    if (oldDocListener) oldDocListener.remove();

    // Add click listener to close dropdown when clicking outside
    const closeDropdownListener = function () {
        menu.classList.remove('show');
    };
    closeDropdownListener.id = 'dropdownDocListener';
    document.addEventListener('click', closeDropdownListener);
}

}
// -------------------------
// Bind sidebar toggle
// -------------------------
$("#topnav-hamburger-icon").on("click", Res);

// -------------------------
// Re-initialize after Livewire navigation
// -------------------------
document.addEventListener('livewire:navigated', Res(0));

// -------------------------
// Initialize on page load
// -------------------------
document.addEventListener('DOMContentLoaded', Res(0));


    
function googleTranslateElementInit() {
    new google.translate.TranslateElement(
        {pageLanguage: 'en'},
        'google_translate_element'
    );
}

function translateLanguage(lang) {
    let iframe = document.querySelector("iframe.goog-te-menu-frame");
    let innerDoc = iframe.contentDocument || iframe.contentWindow.document;
    let items = innerDoc.querySelectorAll(".goog-te-menu2-item span.text");

    items.forEach(function(item) {
        if (item.innerText.toLowerCase().includes(lang.toLowerCase())) {
            item.click();
        }
    });
}

</script>


<script src="{{ asset('public/js/google-translate.js') }}"></script>
<script>
    googleTranslateElementInit();
</script>
