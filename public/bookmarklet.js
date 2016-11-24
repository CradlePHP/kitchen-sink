(function() {
    var start = function() {
        var results = [], output = [];

        //we need to do it here otherwise
        //it will capture the ui red boxes
        var texts = jQuery(document.body)
            .find(':not(iframe,script,style,noscript,option,button)')
            .contents()
            .filter(function() {
                return this.nodeType === 3;
            });

        jQuery('img', document.body).each(function() {
            var data = getImageData(this);

            if(!data) {
                return;
            }

            var line = getLine(data);

            if(output.indexOf(line) !== -1) {
                return;
            }

            results.push(data);
            output.push(line);

            drawBbox(output.length - 1, data);
        });

        texts.each(function() {
                var data = getData(this);

                if(!data) {
                    return;
                }

                var line = getLine(data);

                if(output.indexOf(line) !== -1) {
                    return;
                }

                results.push(data);
                output.push(line);

                drawBbox(output.length - 1, data);
            });

        drawActions(results);
    }

    var initialize = function() {
        //initialize
        if(typeof jQuery !== 'undefined') {
            start(jQuery);
            return;
        }

        //load up jquery
        var script = document.createElement('script');
        script.src = '//s3-ap-southeast-1.amazonaws.com/salaaap-dev/web/bower_components/eve-jquery/jquery.js';
        document.body.appendChild(script);

        var interval = setInterval(function() {
            if(typeof jQuery !== 'undefined') {
                start(jQuery);
                clearInterval(interval);
            }
        }, 600);
    };

    var getData = function(text) {
        if(!text.wholeText.trim().length) {
            return false;
        }

        var node = jQuery(text).parent();
        var weight = node.css('font-weight');

        if(weight === 'normal') {
            weight = 400;
        } else if(weight === 'bold') {
            weight = 600;
        }

        var data = {
            text        : text.wholeText.trim(),
            tag         : node.get(0).tagName,
            x           : Math.round(node.offset().left),
            y           : Math.round(node.offset().top),
            width       : Math.round(node.width()),
            height      : Math.round(node.height()),
            length      : text.wholeText.trim().length,
            size        : Math.round(parseFloat(node.css('font-size').replace('px', ''))),
            color       : node.css('color'),
            style       : node.css('font-style'),
            decoration  : node.css('text-decoration'),
            weight      : weight,
            length      : text.wholeText.trim().length,
            numlength   : text.wholeText.trim().replace(/[^\d]/ig, '').length,
            alphalength : text.wholeText.trim().replace(/[\d]/ig, '').length,
            numratio    : Math.round((text.wholeText.trim().replace(/[^\d]/ig, '').length / text.wholeText.trim().length) * 100) / 100,
            alpharatio  : Math.round((text.wholeText.trim().replace(/[\d]/ig, '').length / text.wholeText.trim().length) * 100) / 100,
            area        : Math.round(node.width()) * Math.round(node.height()),
            coverage    : Math.round(Math.round(node.width()) / text.wholeText.trim().length),
            vertical    : Math.round(node.height()) - Math.round(parseFloat(node.css('font-size').replace('px', ''))),
            target      : 'unknown'
        };

        if(!data.width || !data.height) {
            return false;
        }

        return data;
    };

    var getImageData = function(node) {
        node = jQuery(node);

        var data = {
            text        : node.attr('src'),
            tag         : node.get(0).tagName,
            x           : Math.round(node.offset().left),
            y           : Math.round(node.offset().top),
            width       : Math.round(node.width()),
            height      : Math.round(node.height()),
            length      : 0,
            size        : 0,
            color       : 0,
            style       : 0,
            decoration  : 0,
            weight      : 0,
            length      : 0,
            numlength   : 0,
            alphalength : 0,
            numratio    : 0,
            alpharatio    : 0,
            area        : Math.round(node.width()) * Math.round(node.height()),
            coverage    : 0,
            vertical    : 0,
            target        : 'unknown'
        };

        if(!data.width || !data.height) {
            return false;
        }

        return data;
    };

    var getLine = function(data) {
        if(typeof data.text === 'undefined') {
            return false;
        }

        return [
            data.tag,
            data.x,
            data.y,
            data.width,
            data.height,
            data.size,
            data.weight,
            data.style,
            data.decoration,
            data.length,
            data.numlength,
            data.alphalength,
            data.numratio,
            data.alpharatio,
            data.area,
            data.coverage,
            data.vertical,
            data.target
        ].join(' ');
    };

    var drawBbox = function(id, data) {
        data.bbox = jQuery('<div>')
            .css('position', 'absolute')
            .css('left', data.x)
            .css('top', data.y)
            .css('z-index', 99999)
            .css('border', '1px solid red')
            .css('color', 'red')
            .width(data.width)
            .height(data.height)
            .html(id)
            .appendTo(document.body)
            .hover(function() {
                jQuery('span.salaaap-meta').html(data.text);
            }, function() {

            });
    };

    var drawActions = function(results) {
        jQuery('<div class="salaaap-info">\
            <span class="salaaap-meta" style="display:block;"></span>\
            <textarea class="salaaap-targets" style="height:100px;display:inline-block;"></textarea>\
            <button class="salaaap-process" style="height:100px;display:inline-block;">Process</button>\
            <textarea class="salaaap-results" style="display:none;height:100px;width:500px"></textarea>\
        </div>')
            .css('position', 'fixed')
            .css('bottom', 0)
            .css('left', 0)
            .css('overflow', 'auto')
            .css('left', 0)
            .css('padding', '10px')
            .css('min-height', '100px')
            .css('z-index', 99999)
            .css('background-color', '#EFEFEF')
            .appendTo(document.body);

        jQuery('button.salaaap-process').click(function() {
            jQuery('textarea.salaaap-targets').val().split("\n").forEach(function(target) {
                if(!target.trim().length) {
                    return;
                }

                target = target.trim().split(' ');

                if(target.length !== 2) {
                    return;
                }

                if(target[0].indexOf('+') !== -1) {
                    //we need to combine boxes
                    var ids = target[0].split('+');
                    var mainId = ids.shift();
                    var mainData = results[mainId];
                    ids.forEach(function(id) {
                        var data = results[id];
                        var x1 = mainData.x + mainData.width;
                        var y1 = mainData.y + mainData.height;
                        var x2 = data.x + data.width;
                        var y2 = data.y + data.height;

                        if(data.x < mainData.x) {
                            mainData.x = data.x
                        }

                        if(data.y < mainData.y) {
                            mainData.y = data.y
                        }

                        if(x1 < x2) {
                            mainData.width = x2 - mainData.x;
                        } else {
                            mainData.width = x1 - mainData.x;
                        }

                        if(y1 < y2) {
                            mainData.height = y2 - mainData.y;
                        } else {
                            mainData.height = y1 - mainData.y;
                        }

                        mainData.text = (mainData.text + ' ' + data.text).trim();
                        mainData.length = mainData.text.length;
                        mainData.numlength = mainData.text.replace(/[^\d]/ig, '').length;
                        mainData.alphalength = mainData.text.replace(/[\d]/ig, '').length;
                        mainData.numratio = Math.round((mainData.numlength / mainData.length) * 100) / 100;
                        mainData.alpharatio = Math.round((mainData.alphalength / mainData.length) * 100) / 100;
                        mainData.area = Math.round(mainData.width * mainData.height);
                        mainData.coverage = Math.round(Math.round(mainData.width) / mainData.length);
                        mainData.vertical = Math.round(mainData.height) - Math.round(mainData.size);
                    });

                    target[0] = mainId;
                }

                target[0] = parseInt(target[0]);

                if(typeof results[target[0]] !== 'undefined') {
                    results[target[0]].target = target[1];
                }
            });

            var log = [];
            var template = "INSERT INTO `train` "
                + "(`train_url`, `train_value`, `train_label`, "
                + "`train_x`, `train_y`, `train_width`, `train_height`, "
                + "`train_size`, `train_color`, `train_style`, `train_decoration`, "
                + "`train_weight`, `train_length`, `train_num_length`, "
                + "`train_alpha_length`, `train_num_ratio`, `train_alpha_ratio`, "
                + "`train_area`, `train_horizontal_coverage`, `train_vertical_coverage`) "
                + "VALUES ('{URL}', '{VALUE}', '{LABEL}', '{X}', '{Y}', '{WIDTH}', "
                + "'{HEIGHT}', '{SIZE}', '{COLOR}', '{STYLE}', '{DECORATION}', "
                + "'{WEIGHT}', '{LENGTH}', '{NUM_LENGTH}', '{ALPHA_LENGTH}', "
                + "'{NUM_RATIO}', '{ALPHA_RATIO}', '{AREA}', '{HORIZONTAL_COVERAGE}', "
                + "'{VERTICAL_COVERAGE}');";

            results.forEach(function(data) {
                if(!getLine(data)) {
                    //return;
                }

                if(data.target === 'unknown') {
                    data.bbox.remove();
                    return;
                }

                //log.push(getLine(data));
                log.push(template
                    .replace('{URL}', window.location.href)
                    .replace('{VALUE}', data.text.replace("'", "\\'"))
                    .replace('{LABEL}', data.target)
                    .replace('{X}', data.x)
                    .replace('{Y}', data.y)
                    .replace('{WIDTH}', data.width)
                    .replace('{HEIGHT}', data.height)
                    .replace('{SIZE}', data.size)
                    .replace('{COLOR}', data.color)
                    .replace('{STYLE}', data.style)
                    .replace('{DECORATION}', data.decoration)
                    .replace('{WEIGHT}', data.weight)
                    .replace('{LENGTH}', data.length)
                    .replace('{NUM_LENGTH}', data.numlength)
                    .replace('{ALPHA_LENGTH}', data.alphalength)
                    .replace('{NUM_RATIO}', data.numratio)
                    .replace('{ALPHA_RATIO}', data.alpharatio)
                    .replace('{AREA}', data.area)
                    .replace('{HORIZONTAL_COVERAGE}', data.coverage)
                    .replace('{VERTICAL_COVERAGE}', data.vertical));
            });

            jQuery('textarea.salaaap-results')
                .val(log.join("\n"))
                .css('display', 'inline-block');
        });
    };

    initialize();
})();
