        function allSelected(element) {
                for (var i=0; i<element.options.length; i++) {
                        var o = element.options[i];
                        o.selected = true;
                }
        }
        function addSelectedToList( frmName, srcListName, tgtListName ) {
                var form = eval( 'document.' + frmName );
                var srcList = eval( 'form.' + srcListName );
                var tgtList = eval( 'form.' + tgtListName );

                var srcLen = srcList.length;
                var tgtLen = tgtList.length;
                var tgt = "x";

                //build array of target items
                for (var i=tgtLen-1; i > -1; i--) {
                        tgt += "," + tgtList.options[i].value + ","
                }

                //Pull selected resources and add them to list
                //for (var i=srcLen-1; i > -1; i--) {
                for (var i=0; i < srcLen; i++) {
                        if (srcList.options[i].selected && tgt.indexOf( "," + srcList.options[i].value + "," ) == -1) {
                                opt = new Option( srcList.options[i].text, srcList.options[i].value );
                                tgtList.options[tgtList.length] = opt;
                        }
                }
        }
        function delSelectedFromList( frmName, srcListName ) {
                var form = eval( 'document.' + frmName );
                var srcList = eval( 'form.' + srcListName );
                var srcLen = srcList.length;

                for (var i=srcLen-1; i > -1; i--) {
                        if (srcList.options[i].selected) {
                                srcList.options[i] = null;
                        }
                }
        }
