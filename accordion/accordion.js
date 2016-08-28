accordion: function() {
	$( '.column-slide' ).click( function(){
		var $this = $( this );
		$this.addClass( 'active' );
		$this.siblings().removeClass( 'active' );
		$this.closest( '.layout-accordion' ).removeClass( 'change' );
	});
}

$(function() {
    accordion();
});
