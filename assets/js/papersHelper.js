$('#authors').change(function(e){
    const clones = $('#authors option:selected').clone();
    console.log(clones);
    $('#userId').html('');
    $('#userId').val('');
    $('#userId').append($('<option value="" disabled="disabled">Please choose a presenter</option>'));
    $('#userId').append(clones);
});