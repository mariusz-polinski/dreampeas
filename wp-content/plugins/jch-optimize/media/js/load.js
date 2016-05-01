function JchLoadJquery() {
        var script = document.createElement("script")
        script.type = "text/javascript";

        if (script.readyState) {  //IE
                script.onreadystatechange = function() {
                        if (script.readyState == "loaded" ||
                                script.readyState == "complete") {
                                script.onreadystatechange = null;
                                jQuery.noConflict();
                        }
                };
        } else {  //Others
                script.onload = function() {
                        jQuery.noConflict();
                };
        }

        script.src = "//code.jquery.com/jquery-1.10.2.min.js";
        document.getElementsByTagName("head")[0].appendChild(script);
};