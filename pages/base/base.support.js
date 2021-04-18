$('#clipboard_login_share').click(function () {
    var str = $('#link_login_share').html().trim();
    console.dir(str);

    var area = document.createElement('textarea');

    document.body.appendChild(area);
    area.value = str;
    area.select();
    document.execCommand("copy");
    document.body.removeChild(area);

    alert('Copied to clipboard:\n' + str);
});
 