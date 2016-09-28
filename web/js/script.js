$(document).ready(function(){
    var myApp = new appClass() || {};

    myApp.getCount();

    /*bind add to cart */
    $('.button-order').on('click', function(){
        myApp.addToCart($(this).data('item'));
    });

    /*bind remove from cart */
    $('.button-delete').on('click', function(){
        myApp.removeFromCart($(this).data('item'));
    });

    /* bind change qty */
    $('.group-qty .item-qty').on('click', function(){
        myApp.changeItemQty($(this).data('item'), $(this).data('qty'));
    });

});






