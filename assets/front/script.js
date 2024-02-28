jQuery(document).ready(function($){
    let city = ""
    let startDate = ""
    let endDate = ""
    let guests = 0
    let price = 0
    let page = 0
    $('input[name="espl-search-input-dates"]').daterangepicker({
        opens: 'left',
        minYear: new Date().getFullYear(),
        maxYear: parseInt(moment().format('YYYY'),1),
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
      }, 
    //   function(start, end, label) {
    //     // startDate = start.format('YYYY-MM-DD')
    //     // endDate = end.format('YYYY-MM-DD')
    //     // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    //   }
      );
      $('input[name="espl-search-input-dates"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        startDate = picker.startDate.format('YYYY-MM-DD')
        endDate = picker.endDate.format('YYYY-MM-DD')
    });
  
    $('input[name="espl-search-input-dates"]').on('cancel.daterangepicker', function(ev, picker) {
        startDate = "";
        endDate = "";
        $(this).val("");
    });
      $(document).on("change", ".espl-property-city-input", function(e){
        city = $(this).val()
      })
      $(document).on("blur", ".espl-property-search-price-input", function(e){
        let inputValue = $(this).val(); 
        if ($.isNumeric(inputValue) && inputValue % 1 === 0) {
           price = parseInt(inputValue)
        } else if ($.isNumeric(inputValue) && inputValue % 1 !== 0) {
            price = parseFloat(inputValue)
        } else {
            price = 0
        }
      })
    $(document).on("click", ".espl-input-increment", function(e){
        let currentValue = $(".espl-input-guests-count").val()
        currentValue = parseInt(currentValue);
        guests = currentValue+1
        $(".espl-input-guests-count").val(guests)
        $(".espl-guests-counter-text").text(guests)
        
    })
    $(document).on("click", ".espl-input-decrement", function(e){
        let currentValue = $(".espl-input-guests-count").val()
        currentValue = parseInt(currentValue);
        if(currentValue === 1)
            return;
        guests = currentValue-1
        $(".espl-input-guests-count").val(guests)
        $(".espl-guests-counter-text").text(guests)
    })
    $(document).on("click", ".espl-search-property-button", function(e){
        // e.preventDefault()
        let _this  = $(this);
        let btn_txt = _this.text();
        $.ajax({
            method: 'POST',
            url: myAjax.ajaxurl,
            data: {
                action: 'espl_property_search',
                nonce: $('.espl-property-nonce').val(),
                city,
                startDate, 
                endDate,
                guests,
                price,
                page
            },
            'beforeSend':  () => {
                _this.attr("disabled", true);
                _this.text("Searching...")
                $(".espl-loading-effect-wrapper").html(`<div class="loading-effect"><div class="bar"></div></div>`)
            },
            'success': (res) => {
                if(res.success){
                    displayResult(res.data.rows, res.data.url)
                }else{
                    $(".espl-property-items-wrapper").html(`
                        <h3>Sorry! No propery available for your search</h3>
                    `)
                }
                _this.attr("disabled", false);
                _this.text(btn_txt)
                $(".espl-loading-effect-wrapper").html('')
            },
            'error': (e) => {
                _this.attr("disabled", false);
                _this.text(btn_txt)
                $(".espl-loading-effect-wrapper").html('')
            }
        });        
    })
    function displayResult(rows, url){
        let html = ``;
        let search_date = ""
        if(startDate !== "" && endDate !== ""){
            search_date = startDate+' - '+endDate
        }
        rows.forEach(function(row){
            let p_url = url+row.post_name
            html += `
            <div class="espl-property-single-item">
                        <div class="espl-property-list-item-image">
                            <a href="${p_url}"> 
                                <img src="${row.espl_property_image_link}">
                            </a>
                        </div>
                        <div class="espl-property-single-item-info">
                            <div class="espl-property-title">
                                <h5 class="espl-list-item-title-text">${row.post_title}</h5>
                                <span></span>
                            </div>
                            <div class="espl-property-title">
                            <p class="espl-list-item-address-text"> ${row.espl_property_city}</p>
                            <span></span>
                            </div>
                            <div class="espl-property-list-item-features">
                                <div class="espl-item-feature-icon-wrapper">
                                    <span class="espl-property-item-icon-size">
                                        <i aria-hidden="true" class="fas fa-people-arrows"></i>
                                    </span> ${row.espl_property_max_guests} Max Guests
                                </div>
                                <div class="espl-item-feature-icon-wrapper">
                                    <span class="espl-property-item-icon-size">
                                        <i aria-hidden="true" class="fas fa-shower"></i> 
                                    </span> ${row.espl_property_bathrooms} Bathrooms
                                </div>
                                <div class="espl-item-feature-icon-wrapper">
                                    <span class="espl-property-item-icon-size">
                                        <i aria-hidden="true" class="fas fa-bed"></i>
                                    </span> ${row.espl_property_bedrooms} Bedrooms
                                </div>
                            </div>
                            <div class="espl-property-price">
                                <span class="espl-text-uppercase ">From</span>
                                <h3 class="espl-list-item-price-text">£${row.espl_property_daily_rate}</h3>
                                <span class="espl-text-uppercase">Per Night</span>
                                <p class="espl-property-result-date">${search_date}</p>
                            </div>
                            <a class="espl-search-property-button espl-text-uppercase" href="${p_url}"> More Details</a>
                        </div>
                    </div>
            `;
        })
        $(".espl-property-items-wrapper").html(html)
    }
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
})
