define('echarts/chart/scatter', [
    'require',
    './base',
    '../util/shape/Symbol',
    '../component/axis',
    '../component/grid',
    '../component/dataZoom',
    '../component/dataRange',
    '../config',
    'zrender/tool/util',
    'zrender/tool/color',
    '../chart'
], function (require) {
    var ChartBase = require('./base');
    var SymbolShape = require('../util/shape/Symbol');
    require('../component/axis');
    require('../component/grid');
    require('../component/dataZoom');
    require('../component/dataRange');
    var ecConfig = require('../config');
    ecConfig.scatter = {
        zlevel: 0,
        z: 2,
        clickable: true,
        legendHoverLink: true,
        xAxisIndex: 0,
        yAxisIndex: 0,
        symbolSize: 4,
        large: false,
        largeThreshold: 2000,
        itemStyle: {
            normal: { label: { show: false } },
            emphasis: { label: { show: false } }
        }
    };
    var zrUtil = require('zrender/tool/util');
    var zrColor = require('zrender/tool/color');
    function Scatter(ecTheme, messageCenter, zr, option, myChart) {
        ChartBase.call(this, ecTheme, messageCenter, zr, option, myChart);
        this.refresh(option);
    }
    Scatter.prototype = {
        type: ecConfig.CHART_TYPE_SCATTER,
        _buildShape: function () {
            var series = this.series;
            this._sIndex2ColorMap = {};
            this._symbol = this.option.symbolList;
            this._sIndex2ShapeMap = {};
            this.selectedMap = {};
            this.xMarkMap = {};
            var legend = this.component.legend;
            var seriesArray = [];
            var serie;
            var serieName;
            var iconShape;
            var iconType;
            for (var i = 0, l = series.length; i < l; i++) {
                serie = series[i];
                serieName = serie.name;
                if (serie.type === ecConfig.CHART_TYPE_SCATTER) {
                    series[i] = this.reformOption(series[i]);
                    this.legendHoverLink = series[i].legendHoverLink || this.legendHoverLink;
                    this._sIndex2ShapeMap[i] = this.query(serie, 'symbol') || this._symbol[i % this._symbol.length];
                    if (legend) {
                        this.selectedMap[serieName] = legend.isSelected(serieName);
                        this._sIndex2ColorMap[i] = zrColor.alpha(legend.getColor(serieName), 0.5);
                        iconShape = legend.getItemShape(serieName);
                        if (iconShape) {
                            var iconType = this._sIndex2ShapeMap[i];
                            iconShape.style.brushType = iconType.match('empty') ? 'stroke' : 'both';
                            iconType = iconType.replace('empty', '').toLowerCase();
                            if (iconType.match('rectangle')) {
                                iconShape.style.x += Math.round((iconShape.style.width - iconShape.style.height) / 2);
                                iconShape.style.width = iconShape.style.height;
                            }
                            if (iconType.match('star')) {
                                iconShape.style.n = iconType.replace('star', '') - 0 || 5;
                                iconType = 'star';
                            }
                            if (iconType.match('image')) {
                                iconShape.style.image = iconType.replace(new RegExp('^image:\\/\\/'), '');
                                iconShape.style.x += Math.round((iconShape.style.width - iconShape.style.height) / 2);
                                iconShape.style.width = iconShape.style.height;
                                iconType = 'image';
                            }
                            iconShape.style.iconType = iconType;
                            legend.setItemShape(serieName, iconShape);
                        }
                    } else {
                        this.selectedMap[serieName] = true;
                        this._sIndex2ColorMap[i] = zrColor.alpha(this.zr.getColor(i), 0.5);
                    }
                    if (this.selectedMap[serieName]) {
                        seriesArray.push(i);
                    }
                }
            }
            this._buildSeries(seriesArray);
            this.addShapeList();
        },
        _buildSeries: function (seriesArray) {
            if (seriesArray.length === 0) {
                return;
            }
            var series = this.series;
            var seriesIndex;
            var serie;
            var data;
            var value;
            var xAxis;
            var yAxis;
            var pointList = {};
            var x;
            var y;
            for (var j = 0, k = seriesArray.length; j < k; j++) {
                seriesIndex = seriesArray[j];
                serie = series[seriesIndex];
                if (serie.data.length === 0) {
                    continue;
                }
                xAxis = this.component.xAxis.getAxis(serie.xAxisIndex || 0);
                yAxis = this.component.yAxis.getAxis(serie.yAxisIndex || 0);
                pointList[seriesIndex] = [];
                for (var i = 0, l = serie.data.length; i < l; i++) {
                    data = serie.data[i];
                    value = this.getDataFromOption(data, '-');
                    if (value === '-' || value.length < 2) {
                        continue;
                    }
                    x = xAxis.getCoord(value[0]);
                    y = yAxis.getCoord(value[1]);
                    pointList[seriesIndex].push([
                        x,
                        y,
                        i,
                        data.name || ''
                    ]);
                }
                this.xMarkMap[seriesIndex] = this._markMap(xAxis, yAxis, serie.data, pointList[seriesIndex]);
                this.buildMark(seriesIndex);
            }
            this._buildPointList(pointList);
        },
        _markMap: function (xAxis, yAxis, data, pointList) {
            var xMarkMap = {
                min0: Number.POSITIVE_INFINITY,
                max0: Number.NEGATIVE_INFINITY,
                sum0: 0,
                counter0: 0,
                average0: 0,
                min1: Number.POSITIVE_INFINITY,
                max1: Number.NEGATIVE_INFINITY,
                sum1: 0,
                counter1: 0,
                average1: 0
            };
            var value;
            for (var i = 0, l = pointList.length; i < l; i++) {
                value = data[pointList[i][2]].value || data[pointList[i][2]];
                if (xMarkMap.min0 > value[0]) {
                    xMarkMap.min0 = value[0];
                    xMarkMap.minY0 = pointList[i][1];
                    xMarkMap.minX0 = pointList[i][0];
                }
                if (xMarkMap.max0 < value[0]) {
                    xMarkMap.max0 = value[0];
                    xMarkMap.maxY0 = pointList[i][1];
                    xMarkMap.maxX0 = pointList[i][0];
                }
                xMarkMap.sum0 += value[0];
                xMarkMap.counter0++;
                if (xMarkMap.min1 > value[1]) {
                    xMarkMap.min1 = value[1];
                    xMarkMap.minY1 = pointList[i][1];
                    xMarkMap.minX1 = pointList[i][0];
                }
                if (xMarkMap.max1 < value[1]) {
                    xMarkMap.max1 = value[1];
                    xMarkMap.maxY1 = pointList[i][1];
                    xMarkMap.maxX1 = pointList[i][0];
                }
                xMarkMap.sum1 += value[1];
                xMarkMap.counter1++;
            }
            var gridX = this.component.grid.getX();
            var gridXend = this.component.grid.getXend();
            var gridY = this.component.grid.getY();
            var gridYend = this.component.grid.getYend();
            xMarkMap.average0 = xMarkMap.sum0 / xMarkMap.counter0;
            var x = xAxis.getCoord(xMarkMap.average0);
            xMarkMap.averageLine0 = [
                [
                    x,
                    gridYend
                ],
                [
                    x,
                    gridY
                ]
            ];
            xMarkMap.minLine0 = [
                [
                    xMarkMap.minX0,
                    gridYend
                ],
                [
                    xMarkMap.minX0,
                    gridY
                ]
            ];
            xMarkMap.maxLine0 = [
                [
                    xMarkMap.maxX0,
                    gridYend
                ],
                [
                    xMarkMap.maxX0,
                    gridY
                ]
            ];
            xMarkMap.average1 = xMarkMap.sum1 / xMarkMap.counter1;
            var y = yAxis.getCoord(xMarkMap.average1);
            xMarkMap.averageLine1 = [
                [
                    gridX,
                    y
                ],
                [
                    gridXend,
                    y
                ]
            ];
            xMarkMap.minLine1 = [
                [
                    gridX,
                    xMarkMap.minY1
                ],
                [
                    gridXend,
                    xMarkMap.minY1
                ]
            ];
            xMarkMap.maxLine1 = [
                [
                    gridX,
                    xMarkMap.maxY1
                ],
                [
                    gridXend,
                    xMarkMap.maxY1
                ]
            ];
            return xMarkMap;
        },
        _buildPointList: function (pointList) {
            var series = this.series;
            var serie;
            var seriesPL;
            var singlePoint;
            var shape;
            for (var seriesIndex in pointList) {
                serie = series[seriesIndex];
                seriesPL = pointList[seriesIndex];
                if (serie.large && serie.data.length > serie.largeThreshold) {
                    this.shapeList.push(this._getLargeSymbol(serie, seriesPL, this.getItemStyleColor(this.query(serie, 'itemStyle.normal.color'), seriesIndex, -1) || this._sIndex2ColorMap[seriesIndex]));
                    continue;
                }
                for (var i = 0, l = seriesPL.length; i < l; i++) {
                    singlePoint = seriesPL[i];
                    shape = this._getSymbol(seriesIndex, singlePoint[2], singlePoint[3], singlePoint[0], singlePoint[1]);
                    shape && this.shapeList.push(shape);
                }
            }
        },
        _getSymbol: function (seriesIndex, dataIndex, name, x, y) {
            var series = this.series;
            var serie = series[seriesIndex];
            var data = serie.data[dataIndex];
            var dataRange = this.component.dataRange;
            var rangColor;
            if (dataRange) {
                rangColor = isNaN(data[2]) ? this._sIndex2ColorMap[seriesIndex] : dataRange.getColor(data[2]);
                if (!rangColor) {
                    return null;
                }
            } else {
                rangColor = this._sIndex2ColorMap[seriesIndex];
            }
            var itemShape = this.getSymbolShape(serie, seriesIndex, data, dataIndex, name, x, y, this._sIndex2ShapeMap[seriesIndex], rangColor, 'rgba(0,0,0,0)', 'vertical');
            itemShape.zlevel = serie.zlevel;
            itemShape.z = serie.z;
            itemShape._main = true;
            return itemShape;
        },
        _getLargeSymbol: function (serie, pointList, nColor) {
            return new SymbolShape({
                zlevel: serie.zlevel,
                z: serie.z,
                _main: true,
                hoverable: false,
                style: {
                    pointList: pointList,
                    color: nColor,
                    strokeColor: nColor
                },
                highlightStyle: { pointList: [] }
            });
        },
        getMarkCoord: function (seriesIndex, mpData) {
            var serie = this.series[seriesIndex];
            var xMarkMap = this.xMarkMap[seriesIndex];
            var xAxis = this.component.xAxis.getAxis(serie.xAxisIndex);
            var yAxis = this.component.yAxis.getAxis(serie.yAxisIndex);
            var pos;
            if (mpData.type && (mpData.type === 'max' || mpData.type === 'min' || mpData.type === 'average')) {
                var valueIndex = mpData.valueIndex != null ? mpData.valueIndex : 1;
                pos = [
                    xMarkMap[mpData.type + 'X' + valueIndex],
                    xMarkMap[mpData.type + 'Y' + valueIndex],
                    xMarkMap[mpData.type + 'Line' + valueIndex],
                    xMarkMap[mpData.type + valueIndex]
                ];
            } else {
                pos = [
                    typeof mpData.xAxis != 'string' && xAxis.getCoordByIndex ? xAxis.getCoordByIndex(mpData.xAxis || 0) : xAxis.getCoord(mpData.xAxis || 0),
                    typeof mpData.yAxis != 'string' && yAxis.getCoordByIndex ? yAxis.getCoordByIndex(mpData.yAxis || 0) : yAxis.getCoord(mpData.yAxis || 0)
                ];
            }
            return pos;
        },
        refresh: function (newOption) {
            if (newOption) {
                this.option = newOption;
                this.series = newOption.series;
            }
            this.backupShapeList();
            this._buildShape();
        },
        ondataRange: function (param, status) {
            if (this.component.dataRange) {
                this.refresh();
                status.needRefresh = true;
            }
            return;
        }
    };
    zrUtil.inherits(Scatter, ChartBase);
    require('../chart').define('scatter', Scatter);
    return Scatter;
});define('echarts/component/dataRange', [
    'require',
    './base',
    'zrender/shape/Text',
    'zrender/shape/Rectangle',
    '../util/shape/HandlePolygon',
    '../config',
    'zrender/tool/util',
    'zrender/tool/event',
    'zrender/tool/area',
    'zrender/tool/color',
    '../component'
], function (require) {
    var Base = require('./base');
    var TextShape = require('zrender/shape/Text');
    var RectangleShape = require('zrender/shape/Rectangle');
    var HandlePolygonShape = require('../util/shape/HandlePolygon');
    var ecConfig = require('../config');
    ecConfig.dataRange = {
        zlevel: 0,
        z: 4,
        show: true,
        orient: 'vertical',
        x: 'left',
        y: 'bottom',
        backgroundColor: 'rgba(0,0,0,0)',
        borderColor: '#ccc',
        borderWidth: 0,
        padding: 5,
        itemGap: 10,
        itemWidth: 20,
        itemHeight: 14,
        precision: 0,
        splitNumber: 5,
        splitList: null,
        calculable: false,
        selectedMode: true,
        hoverLink: true,
        realtime: true,
        color: [
            '#006edd',
            '#e0ffff'
        ],
        textStyle: { color: '#333' }
    };
    var zrUtil = require('zrender/tool/util');
    var zrEvent = require('zrender/tool/event');
    var zrArea = require('zrender/tool/area');
    var zrColor = require('zrender/tool/color');
    function DataRange(ecTheme, messageCenter, zr, option, myChart) {
        Base.call(this, ecTheme, messageCenter, zr, option, myChart);
        var self = this;
        self._ondrift = function (dx, dy) {
            return self.__ondrift(this, dx, dy);
        };
        self._ondragend = function () {
            return self.__ondragend();
        };
        self._dataRangeSelected = function (param) {
            return self.__dataRangeSelected(param);
        };
        self._dispatchHoverLink = function (param) {
            return self.__dispatchHoverLink(param);
        };
        self._onhoverlink = function (params) {
            return self.__onhoverlink(params);
        };
        this._selectedMap = {};
        this._range = {};
        this.refresh(option);
        messageCenter.bind(ecConfig.EVENT.HOVER, this._onhoverlink);
    }
    DataRange.prototype = {
        type: ecConfig.COMPONENT_TYPE_DATARANGE,
        _textGap: 10,
        _buildShape: function () {
            this._itemGroupLocation = this._getItemGroupLocation();
            this._buildBackground();
            if (this._isContinuity()) {
                this._buildGradient();
            } else {
                this._buildItem();
            }
            if (this.dataRangeOption.show) {
                for (var i = 0, l = this.shapeList.length; i < l; i++) {
                    this.zr.addShape(this.shapeList[i]);
                }
            }
            this._syncShapeFromRange();
        },
        _buildItem: function () {
            var data = this._valueTextList;
            var dataLength = data.length;
            var itemName;
            var itemShape;
            var textShape;
            var font = this.getFont(this.dataRangeOption.textStyle);
            var lastX = this._itemGroupLocation.x;
            var lastY = this._itemGroupLocation.y;
            var itemWidth = this.dataRangeOption.itemWidth;
            var itemHeight = this.dataRangeOption.itemHeight;
            var itemGap = this.dataRangeOption.itemGap;
            var textHeight = zrArea.getTextHeight('国', font);
            var color;
            if (this.dataRangeOption.orient == 'vertical' && this.dataRangeOption.x == 'right') {
                lastX = this._itemGroupLocation.x + this._itemGroupLocation.width - itemWidth;
            }
            var needValueText = true;
            if (this.dataRangeOption.text) {
                needValueText = false;
                if (this.dataRangeOption.text[0]) {
                    textShape = this._getTextShape(lastX, lastY, this.dataRangeOption.text[0]);
                    if (this.dataRangeOption.orient == 'horizontal') {
                        lastX += zrArea.getTextWidth(this.dataRangeOption.text[0], font) + this._textGap;
                    } else {
                        lastY += textHeight + this._textGap;
                        textShape.style.y += textHeight / 2 + this._textGap;
                        textShape.style.textBaseline = 'bottom';
                    }
                    this.shapeList.push(new TextShape(textShape));
                }
            }
            for (var i = 0; i < dataLength; i++) {
                itemName = data[i];
                color = this.getColorByIndex(i);
                itemShape = this._getItemShape(lastX, lastY, itemWidth, itemHeight, this._selectedMap[i] ? color : '#ccc');
                itemShape._idx = i;
                itemShape.onmousemove = this._dispatchHoverLink;
                if (this.dataRangeOption.selectedMode) {
                    itemShape.clickable = true;
                    itemShape.onclick = this._dataRangeSelected;
                }
                this.shapeList.push(new RectangleShape(itemShape));
                if (needValueText) {
                    textShape = {
                        zlevel: this.getZlevelBase(),
                        z: this.getZBase(),
                        style: {
                            x: lastX + itemWidth + 5,
                            y: lastY,
                            color: this._selectedMap[i] ? this.dataRangeOption.textStyle.color : '#ccc',
                            text: data[i],
                            textFont: font,
                            textBaseline: 'top'
                        },
                        highlightStyle: { brushType: 'fill' }
                    };
                    if (this.dataRangeOption.orient == 'vertical' && this.dataRangeOption.x == 'right') {
                        textShape.style.x -= itemWidth + 10;
                        textShape.style.textAlign = 'right';
                    }
                    textShape._idx = i;
                    textShape.onmousemove = this._dispatchHoverLink;
                    if (this.dataRangeOption.selectedMode) {
                        textShape.clickable = true;
                        textShape.onclick = this._dataRangeSelected;
                    }
                    this.shapeList.push(new TextShape(textShape));
                }
                if (this.dataRangeOption.orient == 'horizontal') {
                    lastX += itemWidth + (needValueText ? 5 : 0) + (needValueText ? zrArea.getTextWidth(itemName, font) : 0) + itemGap;
                } else {
                    lastY += itemHeight + itemGap;
                }
            }
            if (!needValueText && this.dataRangeOption.text[1]) {
                if (this.dataRangeOption.orient == 'horizontal') {
                    lastX = lastX - itemGap + this._textGap;
                } else {
                    lastY = lastY - itemGap + this._textGap;
                }
                textShape = this._getTextShape(lastX, lastY, this.dataRangeOption.text[1]);
                if (this.dataRangeOption.orient != 'horizontal') {
                    textShape.style.y -= 5;
                    textShape.style.textBaseline = 'top';
                }
                this.shapeList.push(new TextShape(textShape));
            }
        },
        _buildGradient: function () {
            var itemShape;
            var textShape;
            var font = this.getFont(this.dataRangeOption.textStyle);
            var lastX = this._itemGroupLocation.x;
            var lastY = this._itemGroupLocation.y;
            var itemWidth = this.dataRangeOption.itemWidth;
            var itemHeight = this.dataRangeOption.itemHeight;
            var textHeight = zrArea.getTextHeight('国', font);
            var mSize = 10;
            var needValueText = true;
            if (this.dataRangeOption.text) {
                needValueText = false;
                if (this.dataRangeOption.text[0]) {
                    textShape = this._getTextShape(lastX, lastY, this.dataRangeOption.text[0]);
                    if (this.dataRangeOption.orient == 'horizontal') {
                        lastX += zrArea.getTextWidth(this.dataRangeOption.text[0], font) + this._textGap;
                    } else {
                        lastY += textHeight + this._textGap;
                        textShape.style.y += textHeight / 2 + this._textGap;
                        textShape.style.textBaseline = 'bottom';
                    }
                    this.shapeList.push(new TextShape(textShape));
                }
            }
            var zrColor = require('zrender/tool/color');
            var per = 1 / (this.dataRangeOption.color.length - 1);
            var colorList = [];
            for (var i = 0, l = this.dataRangeOption.color.length; i < l; i++) {
                colorList.push([
                    i * per,
                    this.dataRangeOption.color[i]
                ]);
            }
            if (this.dataRangeOption.orient == 'horizontal') {
                itemShape = {
                    zlevel: this.getZlevelBase(),
                    z: this.getZBase(),
                    style: {
                        x: lastX,
                        y: lastY,
                        width: itemWidth * mSize,
                        height: itemHeight,
                        color: zrColor.getLinearGradient(lastX, lastY, lastX + itemWidth * mSize, lastY, colorList)
                    },
                    hoverable: false
                };
                lastX += itemWidth * mSize + this._textGap;
            } else {
                itemShape = {
                    zlevel: this.getZlevelBase(),
                    z: this.getZBase(),