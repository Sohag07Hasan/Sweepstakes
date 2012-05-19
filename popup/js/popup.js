/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function($) { 
	    var this_obj = this;
        var id = '#popup-content';

        //Get the screen height and width
        var blanketHeight = $(document).height();
        var blanketWidth = $(window).width();

        //Set heigth and width to blanket to fill up the whole screen
        $('#blanket').css({'width':blanketWidth,'height':blanketHeight});

        //transition effect        
        $('#blanket').fadeIn(1000);    
        $('#blanket').fadeTo("slow",0.8);    

        //Get the window height and width
        var winH = $(window).height();
        var winW = $(window).width();

        //Set the popup window to center
        $(id).css('top',  winH/2-$(id).height()/2);
        $(id).css('left', winW/2-$(id).width()/2);

        //transition effect
        $(id).show(1000);     

    //if close button is clicked
    $('.window .close').click(function (e) {
		 $('#blanket').fadeOut();
         $('.window').slideUp(); 
         
         return false;
        //Cancel the link behavior        
       
    });        

        

});

