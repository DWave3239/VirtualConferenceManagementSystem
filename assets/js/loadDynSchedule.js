if(window.jQuery){
    $.ajax({
        url: 'index.php?main/schedule2/0',
        dataType: 'html',
        success: function(result){
            if(result){
                if($('#schedule2')){
                    $('#schedule2').find('.print-link').attr('href', 'index.php?main/schedule2/0').attr('target', '_blank');
                    $('#schedule2').find('.modal-body').html(result);
                }
                new Scheduler2("mockup_schedule2_full_dynamic", true);
            }
        }
    });
}else{
    var ms2 = new Scheduler("mockup_schedule2_full_dynamic");
    setInterval(function() {
        if ( new Date().getSeconds() === 0 ) ms2.doHighlights();
    }, 1000);
}