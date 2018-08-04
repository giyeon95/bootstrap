$('.inputbox').on('itemAdded',function(event) {
    var text = $(".inputbox").val();
        console.log(text);
        $(".val code").html(text); 
});

$('.inputbox').on('itemRemoved', function(event) {
    var text = $(".inputbox").val();
        console.log(text);
        $(".val code").html(text); 
});