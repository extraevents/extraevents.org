var ready = false;

var Module = {
    "onRuntimeInitialized": function () {
        //initialize scrambler
        var initialize = Module.cwrap("initialize", "void");

        postMessage(["initializing"]);
        initialize();
        postMessage(["initialized"]);

        ready = true;
    }
};

importScripts("scrambler.js");

this.addEventListener("message",
        function (d) {
            if (!ready) {
                return;
            }

            if (d.data[0] == "run") {
                var generateScramble = Module.cwrap("generateScramble", "string");

                var n = d.data[1];
                for (var i = 0; i < n; i++) {
                    var scramble = generateScramble();
                    postMessage(["scramble", scramble, i + 1]);
                }
            } else {
                Module.print("Bad message.");
            }
        },
        false);
