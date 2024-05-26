    // Function to handle slideshow
function handleSlideShow(slideClassName, slideIndexVar) {
    var slideIndex = slideIndexVar;
    showDivs(slideIndex, slideClassName);
  
    function plusDivs(n) {
      showDivs((slideIndex += n), slideClassName);
    }
  
    function showDivs(n, slideClassName) {
      var i;
      var x = document.getElementsByClassName(slideClassName);
      if (n > x.length) {
        slideIndex = 1;
      }
      if (n < 1) {
        slideIndex = x.length;
      }
      for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
      }
      x[slideIndex - 1].style.display = "block";
    }
  }
  
  // Call the function for each slideshow
  handleSlideShow("slide1", 1);
  handleSlideShow("slide2", 1);
  handleSlideShow("slide3", 0);
  handleSlideShow("slide4", 1);
  
    
    //====================================================================
    var bag = []; // initialize price array
    // Animation:
    $(document).ready(function () {
      $(".cart").click(function () {
        $(".container").fadeToggle();
      });
    
      $(".checkout").addClass("disabled");
      $("#bin").addClass("disabled");
    });
    
    $(document).on("click", ".add-to-cart", function (e) {
      e.preventDefault();
      $(".empty").hide();
    
      //---------------------------------------------------------
      if ($(this).hasClass("disable")) {
        //disable mutiple clicks
        return false;
      }
      $(document).find(".add-to-cart").addClass("disable");
      //---------------------------------------------------------
    
      var parent = $(this).parents(".snip");
      var src = parent.find("img").attr("src");
      var cart = $(document).find(".cart");
    
      var posTop = parent.offset().top; //return the coordinates of a element
      var posLeft = parent.offset().left;
    
      $("<img />", {
        class: "animation-fly",
        src: src
      })
        .appendTo("body")
        .css({
          top: posTop,
          left: parseInt(posLeft)
        });
    
      setTimeout(function () {
        $(document).find(".animation-fly").css({
          top: cart.offset().top,
          left: cart.offset().left
        });
        setTimeout(function () {
          $(document).find(".animation-fly").remove(); //after fly
          var quantity = parseInt(cart.find("#count-item").data("count")) + 1;
          //       if(quantity<2){
          //         cart.find('#count-item').text(quantity + ' item').data('count', quantity);
          //       }else{
          //         cart.find('#count-item').text(quantity + ' items').data('count', quantity);
          //       }
          cart
            .find("#count-item")
            .text(quantity + (quantity < 2 ? " item" : " items"))
            .data("count", quantity);
    
          //add item to cart
          var name = parent.find("h4").text();
          var price = parent.find(".real").text();
          var txt3 = document.createElement("hr");
          var txt4 = document.createElement("hr");
    
          $(".col1-name").append(name, txt3);
          $(".col2-price").append(price, txt4);
          $(".checkout").addClass("add-animation");
          $(".checkout").removeClass("disabled");
    
          $("#bin").addClass("add-animation2");
          $("#bin").removeClass("disabled");
    
          //find total amount
          var price2 = parseFloat(parent.find(".real").data("price")); //get "data-price" from <div class="real">
          bag.push(price2);
          var total = 0;
          $(".total-amount").text(function () {
            for (var i in bag) {
              total += bag[i]; //calculate sum of all numbers in the array
            }
            var last = "$ " + total.toFixed(2);
            $(".pay-last").text(last);
            return last; // return total only -> bug.
          });
    
          $(document).find(".add-to-cart").removeClass("disable");
        }, 1000);
      }, 500);
    
      $(".bin").on("click", function () {
        $(".col1-name").empty();
        $(".col2-price").empty();
        $(".empty").show();
        $(".checkout").removeClass("add-animation");
        $("#bin").removeClass("add-animation2");
        $(".checkout").addClass("disabled");
        $("#bin").addClass("disabled");
        bag = [];
        $(".total-amount")
          .add(".pay-last")
          .text("$ " + bag.length);
        cart
          .find("#count-item")
          .text(0 + " item")
          .data("count", 0);
      });
    });
    
    //------------------BILL----------------------
    
    $(document).ready(function () {
      $("#coupon").on("click", function () {
        alert(
          "minhquanghust.blogspot.com Coupon Code:" +
            "\n" +
            "25% off: 0511Q-1601CV" +
            "\n" +
            "15% off: 0511Q-1701NA" +
            "\n" +
            "10% off: 0511Q-1901QA"
        );
      });
    });
    