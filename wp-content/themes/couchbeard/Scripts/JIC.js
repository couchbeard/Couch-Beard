/*!
 * JIC JavaScript Library v1.0
 * https://github.com/brunobar79/J-I-C/
 *
 * Copyright 2012, Bruno Barbieri
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Date: Sat Mar 24 15:11:03 2012 -0200
 */



/**
 * Create the jic object.
 * @constructor
 */

var jic = {
        /**
         * Receives an Image Object (can be JPG OR PNG) and returns a new Image Object compressed
         * @param {Image} source_img_obj The source Image Object
         * @param {Integer} quality The output quality of Image Object
         * @return {Image} result_image_obj The compressed Image Object
         */

        compress: function(source_img_obj, quality){
             var cvs = document.createElement('canvas');
             cvs.width = source_img_obj.naturalWidth;
             cvs.height = source_img_obj.naturalHeight;
             var ctx = cvs.getContext("2d").drawImage(source_img_obj, 0, 0);
             var newImageData = cvs.toDataURL("image/jpeg", quality);
             var result_image_obj = new Image();
             result_image_obj.src = newImageData;
             return result_image_obj;
        },

        /**
         * Receives an Image Object and upload it to the server via ajax
         * @param {Image} compressed_img_obj The Compressed Image Object
         * @param {String} The server side url to send the POST request
         * @param {String} file_input_name The name of the input that the server will receive with the file
         * @param {String} filename The name of the file that will be sent to the server
         * @param {function} the callback to trigger when the upload is finished.
         */

        upload: function(compressed_img_obj, upload_url, file_input_name, filename, callback){
            var cvs = document.createElement('canvas');
            cvs.width = compressed_img_obj.naturalWidth;
            cvs.height = compressed_img_obj.naturalHeight;
            var ctx = cvs.getContext("2d").drawImage(compressed_img_obj, 0, 0);
            
            //ADD sendAsBinary compatibilty to older browsers
            if (XMLHttpRequest.prototype.sendAsBinary === undefined) {
                XMLHttpRequest.prototype.sendAsBinary = function(string) {
                    var bytes = Array.prototype.map.call(string, function(c) {
                        return c.charCodeAt(0) & 0xff;
                    });
                    this.send(new Uint8Array(bytes).buffer);
                };
            }

            var type= 'image/jpeg';
            var data = cvs.toDataURL(type);
            data = data.replace('data:' + type + ';base64,', '');
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', upload_url, true);
            var boundary = 'someboundary';

            xhr.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
            xhr.sendAsBinary(['--' + boundary, 'Content-Disposition: form-data; name="' + file_input_name + '"; filename="' + filename + '"', 'Content-Type: ' + type, '', atob(data), '--' + boundary + '--'].join('\r\n'));
            
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status==200) {
                	callback(this.responseText);
                }
            };


        }
};