$(document).ready(function() {
    $(".follow").on("click", function() {
        const akapit = $(this);
        $.post("changeFollowed.php", { idWydarzenia: "'" + akapit.data("wydarzenie") + "'"}, function(data) {
            if (data == "sukces") {
            akapit.text((akapit.text() == "Dodaj do obserwowanych") ? "Usuń z obserwowanych" : "Dodaj do obserwowanych");;
            }
        });
    });
});
       