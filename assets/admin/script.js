window.addEventListener("load", function(){
    // Store the tabs variables
    let tabs = document.querySelectorAll("ul.espl-nav-tabs > li");
    for(let i = 0; i < tabs.length; i++){
        let tab = tabs[i];
        tab.addEventListener("click", switchTab);
    }
    function switchTab(event){
        event.preventDefault();
        document.querySelector("ul.espl-nav-tabs li.active").classList.remove("active");
        document.querySelector(".espl-tab-content .espl-tab-pane.active").classList.remove("active");
        let clickedTab = event.currentTarget;
        let anchor = event.target;
        let activePaneID = anchor.getAttribute("href");
        clickedTab.classList.add("active");
        document.querySelector(activePaneID).classList.add("active");
    }
});

jQuery(document).ready(function($){
    $(document).on("click", "#espl_update_properties", function(e){
        e.preventDefault();
        let _this  = $(this);
        $.ajax({
            method: 'POST',
            url: ajaxurl,
            data: {
                action: 'espl_property_update',
                nonce: $('#espl_ajax_nonce').val(),
                purpose: 'update_properties',
                data: "Here is my data"
            },
            'beforeSend':  () => {
                _this.attr("disabled", true);
            },
            'success': (res) => {
                sendAlert(res.data.msg, $('.espl-server-response'))
                _this.attr("disabled", false);
            },
            'error': (e) => {
                _this.attr("disabled", false);
            }
        });        
    });  
    function sendAlert(msg, alertElem, _duration = 10000){
        var html = `<div class="alert my-alart alert-warning alert-dismissible">
            <strong>${msg}</strong> 
            <button class="btn btn-danger close" type="button"  data-bs-dismiss="alert" aria-label="close">&times;</button> 
          </div> `; 
        alertElem.html(html);
        if(_duration){
          setTimeout(
            function(){
              $(".my-alart").fadeOut('slow');
            } , _duration
            );
        }  
    }  
});

