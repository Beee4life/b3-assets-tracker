jQuery(document).ready(function () {
    var asset_type = document.getElementById('asset-types');
    asset_type.getElementsByClassName('anchor')[0].onclick = function(evt) {
        if (asset_type.classList.contains('visible'))
            asset_type.classList.remove('visible');
        else
            asset_type.classList.add('visible');
    }

    var asset_group = document.getElementById('asset-groups');
    asset_group.getElementsByClassName('anchor')[0].onclick = function(evt) {
        if (asset_group.classList.contains('visible'))
            asset_group.classList.remove('visible');
        else
            asset_group.classList.add('visible');
    }
});
