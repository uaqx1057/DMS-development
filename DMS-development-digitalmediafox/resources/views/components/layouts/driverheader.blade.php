<div class="">
    <div class="card shadow">
    <div class="card-body" style="padding: 0px 12px;">
    <div class="row">
       <div class="d-flex justify-content-between align-items-center">
    <div>
        <img style="max-width: 100px; padding: 8px;" src="{{ asset('public/logo.png') }}" alt="Speed Logistic">
    </div>
    <div>
       <style>
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
   
}
.goog-te-combo {

    margin: 4px 0;
    width: 134px;
    border: 1px solid #7c3f87;
    padding: 5px;
    margin-top: 8px;
    border-radius: 6px;
    color: #7c3f87;

}
.goog-te-gadget {
    color: white !important;
}

</style>
<!-- Hidden Google Translate element -->
<div id="google_translate_element"></div>

<script>
function googleTranslateElementInit() {
    new google.translate.TranslateElement(
        {pageLanguage: 'en'},
        'google_translate_element'
    );

    // Apply saved language after load
    setTimeout(applySavedLanguage, 1000);
}

function translateLanguage(lang) {
    let iframe = document.querySelector("iframe.goog-te-menu-frame");
    if (!iframe) {
        alert("Google Translate not loaded yet, try again...");
        return;
    }

    let innerDoc = iframe.contentDocument || iframe.contentWindow.document;
    let items = innerDoc.querySelectorAll(".goog-te-menu2-item span.text");

    items.forEach(function(item) {
        if (item.innerText.toLowerCase().includes(lang.toLowerCase())) {
            item.click();
            localStorage.setItem("preferred_lang", lang); // save selected
        }
    });
}

function applySavedLanguage() {
    let savedLang = localStorage.getItem("preferred_lang");
    if (savedLang) {
        translateLanguage(savedLang);
    }
}
</script>

<!-- Load Google Translate -->
<script src="{{ asset('public/js/google-translate.js') }}"></script>
<script>
    googleTranslateElementInit();
</script>

    </div>
</div>

        
    </div>
    </div>
    </div>



</div>