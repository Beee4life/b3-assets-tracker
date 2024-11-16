jQuery(document).ready(function () {
    var asset_group = document.getElementById('asset-groups');
    var asset_type = document.getElementById('asset-types');
    var dates = document.getElementById('dates');

    if ( asset_type ) {
        asset_type.getElementsByClassName('anchor')[0].onclick = function(evt) {
            if (asset_type.classList.contains('visible'))
                asset_type.classList.remove('visible');
            else
                asset_type.classList.add('visible');
            if (asset_group.classList.contains('visible'))
                asset_group.classList.remove('visible');
        }
    }

    if ( asset_group ) {
        asset_group.getElementsByClassName('anchor')[0].onclick = function(evt) {
            if (asset_group.classList.contains('visible'))
                asset_group.classList.remove('visible');
            else
                asset_group.classList.add('visible');
            if (asset_type.classList.contains('visible'))
                asset_type.classList.remove('visible');
        }
    }

    if ( dates ) {
        dates.getElementsByClassName('anchor')[0].onclick = function(evt) {
            if (dates.classList.contains('visible'))
                dates.classList.remove('visible');
            else
                dates.classList.add('visible');
        }
    }
});
