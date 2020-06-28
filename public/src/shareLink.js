(function(){
    "use strict";
    
    function copyLink(e, callback){
        e.preventDefault();
        var el = document.createElement("textarea");
        el.value = window.location.href;
        document.body.appendChild(el);
        el.select();

        try {
            document.execCommand("copy");
        }
        catch (e){
            return callback(e);
        }
        finally {
            document.body.removeChild(el);
        }

        return callback(null);
    }

    document.addEventListener("DOMContentLoaded", function(){
        document.getElementsByClassName("copy-link-btn")[0].addEventListener("click", function(e){
            copyLink(e, function(err){
                if (err) return alert("Konnte URL nicht kopieren! Browser verhindert Zugriff auf die Zwischenablage =(");
                alert("Link in die Zwischenablage kopiert!");
            });
        });
    });
})();
