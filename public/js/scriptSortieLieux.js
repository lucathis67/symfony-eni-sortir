$(document).ready(function () {
    var selectLieux = $("#sortie_lieu");
    var selectVille = $("#sortie_ville");
    var lieux;

    selectVille.change(function () {
        reinitialiserSelectLieux();
        if ($(this).val()) {
            chargerLieux($(this).val(), remplirSelectLieux);
        } else {
            selectLieux.prop("disabled", true);
        }
        viderChampsLieu();
    });

    // Lors d'un retour sur la page dû à une erreur dans le formulaire
    // appelle les fonctions indispensables afin de rétablir les valeurs d'avant le submit
    if (selectLieux.val()) {
        chargerLieux(selectVille.val(), null);
        donnerIndexOptionsLieux();
        selectLieux.prop("disabled", false);
        remplirChampsLieu(selectLieux);
    }

    selectLieux.change(function () {
        if ($(this).val()) {
            remplirChampsLieu()
        } else {
            viderChampsLieu();
        }
    });

    // Peut être utilisé seulement si chargerLieux() a été appellé
    function remplirChampsLieu() {
        var lieuSelected = lieux[$('option:selected', selectLieux).data('index')];
        $('#sortie_rue').val(lieuSelected.rue);
        $('#sortie_codePostal').val(lieuSelected.ville.codePostal);
        $('#sortie_latitude').val(lieuSelected.latitude);
        $('#sortie_longitude').val(lieuSelected.longitude);
    }

    // Vide les champs si aucun lieu n'est séléctionné
    function viderChampsLieu() {
        $('#sortie_rue').val('');
        $('#sortie_codePostal').val('');
        $('#sortie_latitude').val('');
        $('#sortie_longitude').val('');
    }

    // Est appelée en cas de changement de lieu au de non selection d'une ville
    function reinitialiserSelectLieux() {
        selectLieux.children().remove();
        selectLieux.append(
            "<option value>Choisissez un lieu</option>"
        );
    }

    // Permet de charger les lieux en fonction de la ville selectionnée
    // Et si la fonction est appelée lors d'un changement de ville f=remplirSelectLieux
    // Sinon elle est appelée lors d'un retour sur la page dû à une erreur dans le formulaire
    // et alors nul besoin de remplir le select des lieux car chargé automatiquement
    function chargerLieux(villeId, f) {
        $.ajax({
            type: 'GET',
            url: '/symfony/Sortir/public/api/ville/lieux/' + villeId,
            async: false,
            success: function (data) {
                lieux = data;
                if (f) {
                    f(data);
                }
            }
        });
    }

    function remplirSelectLieux(data) {
        $.each(data, function (index, value) {
            selectLieux.append(
                "<option data-index='" + index + "' value='" + value.id + "'>" + value.nom + "</option>"
            );
        });
        selectLieux.prop("disabled", false);
    }

    // Fonction appelée pour donner un data-index au option du select des lieux
    // lors d'un retour sur la page dû à une erreur dans le formulaire
    // Ce data-index est indispensable pour remplirChampsLieu()
    function donnerIndexOptionsLieux() {
        var i = -1; // Je donne l'index -1 à la première option "Choisissez un lieu"
        selectLieux.children().each(function () {
            $(this).data("index", i++);
        })
    }

});