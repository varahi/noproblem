/**
 * Create dependable list with this function 
 * @param {*} parentList-id
 * @param {*} childList-id
 */
function createDependableList(parentList, childList) {
    var options = $(childList+' option');

    $(parentList).change( function() {
        $(childList).empty();
       
        options.each((index, option)=> {

            if ( $(option).data('list-id') == this.value /* || $(option).data('list-id') == 0 */ ) {
                option.selected = option.selected ? true : false;
                
                $(childList).append(option);
            }  

            $(childList).trigger('change');
        })

        if ( $(childList+' option').length === 0 ) {
            $(childList).append('<option value="">No option available..</option>');
        }

    })
}
