/* appClass - main class for the cart app
 *
 */
var appClass = function(){
    console.log('appClass');

    /* getCount will ajax call the route /cart/count where CartController will return the quantity of elements in the cart
     *
     */
    this.getCount = function(){
        var self = this;
        this.ajaxCall('count', 0);
    };

    /* addToCart will ajax call the route /cart/add/{id} where CartController will add the element to the cart
     * @param item_id - the id of the product we add
     *
     */
    this.addToCart = function(item_id){
        var self = this;

        this.ajaxCall('add', item_id);
    };

    /* removeFromCart will ajax call the route /cart/remove/{id} where CartController will remove the element from the cart
     * @param item_id - the id of the cart item we remove
     *
     */
    this.removeFromCart = function(item_id){
        var self = this;

        console.log('removeFromCart');
        this.ajaxCall('remove', item_id);
        console.log('reload');
    };

    /* changeItemQty will ajax call the route /cart/qty/{id}/{qty} where CartController will modify the quantity of the element
     * @param item_id - the id of the cart item we modify
     * @param qty - the new quantity
     *
     */
    this.changeItemQty = function(item_id, qty){
        var self = this;

        console.log('changeQty');
        this.ajaxCall('qty', item_id, qty);
        console.log('reload');
    };
    /* ajaxCall
     * @param target - the action we call from CartController
     * @param item_id - the id of the product/cart item
     * @param qty - optional, the new quantity passed
     *
     */
    this.ajaxCall = function(target, item_id, qty){
        var self = this;
        qty = qty || null;
        if('remove' === target  || 'qty' === target) {
            $('.loader').css('display', 'block');
        }
        $.ajax({
            type: "POST",
            url: "/cart/" + target + "/" +item_id+(qty ? "/"+qty : ""),
            data: {
            },
            dataType: "json",
            success: function(response) {
                self.updateCartCount(response.count);
                $('.loader').css('display', 'none');
                if('remove' === target  || 'qty' === target){
                    location.reload();
                }
            }
        });
    };

    /* updateCartCount wii updat the right top corner cart icon withe the current quantity of elements we have in the cart
     * @param new_value - quantity of elements in the cart
     *
     */

    this.updateCartCount = function(new_value){
        var self = this;
        $('.header .cart-count').css('visibility', 'visible');
        $('.header .cart-count').html(new_value);
        if(0 == new_value){
            $('.header .cart-count').css('visibility', 'hidden');
        }
    };

};