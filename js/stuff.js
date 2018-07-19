/* Loader popup */
var loader = {
    obj : $('#loader'),
    init : function(mode)
    {
        if (!this.obj.length) this.create();
        eval('this.obj.' + mode + '();');
    },
    create : function()
    {
        var self = this, setXY = function(e)
        {
            self.position(e.pageX, e.pageY);
        };
        this.obj = $('<div id="loader">Ждите, идёт загрузка</div>');
        $('body').append(this.obj);
        $(document).bind('mousemove.loader', setXY).bind('click.loader', setXY);
    },
    position : function(x, y)
    {
        this.obj.css({left: x + 15, top: y + 25});
    }
};

/* Default init */
$.fn.jsInit = function(params)
{
    var o = $(this);

    for (var key in params)
    {
        var a = params[key];
        switch(a.name)
        {
            case 'all':
            {
                /* Context search */
                $('div.iSearch_cs').each(function()
                {
                    var self = $(this), body = $('div.search_body', this), drop = $('div.search_list', self);
                    var close = function()
                    {
                        body.removeClass('opened');
                    };
                    var action = function(event)
                    {
                        if ($(this).val().length > 2)
                        {
                            var text = encodeURI($(this).val());

                            loader.init('show');
                            $.get('?cmd=match&q=' + text, function(data)
                            {
                                var target = '';
                                data = JSON.parse(data);
                                loader.init('hide');
                                if (data.error)
                                {
                                    drop.html(data.error);
                                    return false;
                                }

                                for (var i = 0; i < data.length; i++)
                                {
                                    target += "<li><a class='view' href='?cmd=getitem&id=" + data[i].id + "' title='View'>" + data[i].name + "</a> Наличие: " + data[i].isAvailable + ", Цена: " + data[i].price + "</li>";
                                }
                                if (target)
                                {
                                    drop.html('<ul>' + target + '</ul>');
                                    body.addClass('opened');
                                }
                                else close();

                            });
                        }
                        else
                        {
                            drop.html(''); close();
                        }
                    }
                    $('input', self).focus(action).bind('input propertychange', action);
                    self.click(function(event)
                    {
                        event.stopPropagation();
                    });
                    $('html').bind('click.iSearch_cs', close);
                });

                /* Category expander handler */
                $('ul.catList').on('click', 'li > a.expand', function()
                {
                    var self = $(this);
                    if (self.hasClass("selected"))    return false;     // TODO: back collapser implements here
                    loader.init('show');
                    $.ajax(
                    {
                        url: this

                    }).done(function(data, textStatus, jqXHR)
                    {
                        var target = '', plus = '', allP = '';
                        data = JSON.parse(data);
                        loader.init('hide');
                        if (data.error)
                        {
                            $('#content').html(data.error);
                            return false;
                        }

                        for (var i = 0; i < data.length; i++)
                        {
                            if (data[i].children > 0)
                            {
                                plus = "&nbsp;<a class='expand' href='?cmd=expand&id=" + data[i].id + "' title='Expand'>+</a>&nbsp;";
                                allP = "&nbsp;(<a class='view' href='?cmd=viewall&id=" + data[i].id + "' title='View'>Все товары раздела</a>)&nbsp;";
                            }
                            else
                            {
                                plus = allP = "&nbsp;&nbsp;&nbsp;";
                            }
                            target += "<li>" + plus + "<a class='view' href='?cmd=viewcat&id=" + data[i].id + "' title='View'>" + data[i].name + "</a></li>";
                        }
                        if (target)
                        {
                            self.parent().append('<ul>' + target + '</ul>');
                            self.addClass("selected");
                            self.hide();
                        }

                    }).fail(function(jqXHR, textStatus, errorThrown)
                    {
                        loader.init('hide');
                        alert('Run-time error: ' + jqXHR.responseText);
                    });
                    return false;
                });

                /* Brand / category click handler */
                $('ul.brandList, ul.catList').on('click', 'li > a.view', function()
                {
                    var self = $(this);
                    loader.init('show');
                    $.ajax(
                    {
                        url: this

                    }).done(function(data, textStatus, jqXHR)
                    {
                        var target = '';
                        data = JSON.parse(data);
                        loader.init('hide');
                        //alert(data.toSource());
                        if (data.error)
                        {
                            $('#content').html(data.error);
                            return false;
                        }

                        for (var i = 0; i < data.length; i++)
                        {
                            target += "<li><a class='view' href='?cmd=getitem&id=" + data[i].id + "' title='View'>" + data[i].name + "</a> Наличие: " + data[i].isAvailable + ", Цена: " + data[i].price + "</li>";
                        }
                        if (target)
                        {
                            $('#content').html('<ul>' + target + '</ul>');
                        }

                    }).fail(function(jqXHR, textStatus, errorThrown)
                    {
                        loader.init('hide');
                        alert('Run-time error: ' + jqXHR.responseText);
                    });
                    return false;
                });

                /* Product click handler */
                $('#content, div.search_list').on('click', 'ul > li > a.view', function()
                {
                    var self = $(this);
                    loader.init('show');
                    $.ajax(
                    {
                        url: this

                    }).done(function(data, textStatus, jqXHR)
                    {
                        data = JSON.parse(data);
                        loader.init('hide');

                        var scrollPos = $(window).scrollTop();

		                /* Show the correct popup box, show the blackout and disable scrolling */
		                $('#popup-box').show();
		                $('#blackout').show();
		                $('html,body').css('overflow', 'hidden');

		                /* Fixes a bug in Firefox */
		                $('html').scrollTop(scrollPos);

                        if (data.error)
                        {
                            $('div.top').html("Error!");
                            $('div.bottom').html(data.error);
                            return false;
                        }

                        $('div.top').html(data.name);
                        $('div.bottom').html("<ul><li>Наличие: <b>" + data.isAvailable + "</b></li><li>Цена: <b>" + data.price + "</b></li></ul>");

                    }).fail(function(jqXHR, textStatus, errorThrown)
                    {
                        loader.init('hide');
                        alert('Run-time error: ' + jqXHR.responseText);
                    });
                    return false;
                });

                $('html').click(function()
                {
                    var scrollPos = $(window).scrollTop();
                	/* Hide the popup and blackout when clicking outside the popup */
                	$('#popup-box').hide();
                	$('#blackout').hide();
                	$("html,body").css("overflow","auto");
                	$('html').scrollTop(scrollPos);
                });
                $('.close').click(function()
                {
                	var scrollPos = $(window).scrollTop();
                	/* Similarly, hide the popup and blackout when the user clicks close */
                	$('#popup-box').hide();
                	$('#blackout').hide();
                	$("html,body").css("overflow","auto");
                	$('html').scrollTop(scrollPos);
                });

                /* Popup div */

                $('body').append('<div class="popup-box" id="popup-box"><div class="close">X</div><div class="top"></div><div class="bottom"></div></div>');
	            $('body').append('<div id="blackout"></div>');

	            var boxWidth = 400;

                function centerBox()
                {
                	var winWidth = $(window).width();
                	var winHeight = $(document).height();
                	var scrollPos = $(window).scrollTop();

                	var disWidth = (winWidth - boxWidth) / 2
                	var disHeight = scrollPos + 150;

                	$('.popup-box').css({'width' : boxWidth+'px', 'left' : disWidth+'px', 'top' : disHeight+'px'});
                	$('#blackout').css({'width' : winWidth+'px', 'height' : winHeight+'px'});

                	return false;
                }

                $(window).resize(centerBox);
                $(window).scroll(centerBox);
                centerBox();
                break;
            }
        }
    }
};
