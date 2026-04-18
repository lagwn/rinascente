(function (root, factory) {
    if (typeof module === 'object' && module.exports) {
        module.exports = factory();
        return;
    }
    root.YumehoPricing = factory();
}(typeof self !== 'undefined' ? self : this, function () {
    function buildDefaultCeilingRailLengthOptions() {
        return [5, 10, 20];
    }

    function sanitizeRailLengthOptions(values) {
        var unique = {};
        var options = Array.isArray(values) ? values : [];

        options.forEach(function (value) {
            var parsed = Number(value);
            if (!Number.isFinite(parsed)) {
                return;
            }

            parsed = Math.round(parsed);
            if (parsed > 0) {
                unique[parsed] = parsed;
            }
        });

        return Object.keys(unique)
            .map(function (value) { return Number(value); })
            .sort(function (left, right) { return left - right; });
    }

    var DEFAULT_OPTIONS = Object.freeze({
        jrx: Object.freeze({
            label: 'JRX（Junction Rail eXpress）方向転換システム',
            shortLabel: 'JRX',
            price: 350000,
            unitLabel: '台',
            maxQuantity: 1,
            selectionType: 'checkbox'
        }),
        pulling: Object.freeze({
            label: 'T-Pulling（プーリングシステム）',
            shortLabel: 'T-Pulling',
            price: 300000,
            unitLabel: '台',
            maxQuantity: 5,
            selectionType: 'quantity'
        }),
        sling: Object.freeze({
            label: 'T-Sling（スリングシステム）',
            shortLabel: 'T-Sling',
            price: 250000,
            unitLabel: '台',
            maxQuantity: 5,
            selectionType: 'quantity'
        }),
        gcord: Object.freeze({
            label: 'G-Cord（自動高さ調整）',
            shortLabel: 'G-Cord',
            price: 280000,
            unitLabel: '台',
            maxQuantity: 5,
            selectionType: 'quantity'
        }),
        sng: Object.freeze({
            label: 'SnG（ロック機構）',
            shortLabel: 'SnG',
            price: 150000,
            unitLabel: '台',
            maxQuantity: 5,
            selectionType: 'quantity'
        }),
        measure: Object.freeze({
            label: '歩行データ計測キット（PC連携）',
            shortLabel: '計測キット',
            price: 200000,
            unitLabel: '台',
            maxQuantity: 1,
            selectionType: 'quantity'
        })
    });

    var DEFAULT_SYSTEMS = Object.freeze([
        Object.freeze({
            code: 'fcw-3000',
            displayName: '天井直付型 FCW-3000',
            shortName: 'FCW-3000',
            spec: 'カスタム設計 / 周回・直線レール対応',
            installType: 'ceiling',
            maxRailLength: 0,
            railLengthOptions: Object.freeze(buildDefaultCeilingRailLengthOptions()),
            unitPrice: 950000,
            railPricePerM: 30000,
            resultName: '天井直付型 FCW-3000',
            railLabel: ''
        }),
        Object.freeze({
            code: 'pgt-9000',
            displayName: 'スタンド型 PGT-9000',
            shortName: 'PGT-9000',
            spec: '2000×4000mm / 総レール長14m',
            installType: 'stand',
            maxRailLength: 14,
            railLengthOptions: Object.freeze([14]),
            unitPrice: 1150000,
            railPricePerM: 30000,
            resultName: 'スタンド型 PGT-9000（2000×4000mm）',
            railLabel: '総レール長 14m'
        }),
        Object.freeze({
            code: 'pgt-9001',
            displayName: 'スタンド型 PGT-9001',
            shortName: 'PGT-9001',
            spec: '2000×6000mm / 総レール長20m',
            installType: 'stand',
            maxRailLength: 20,
            railLengthOptions: Object.freeze([20]),
            unitPrice: 1150000,
            railPricePerM: 30000,
            resultName: 'スタンド型 PGT-9001（2000×6000mm）',
            railLabel: '総レール長 20m'
        })
    ]);

    function toNumber(value, fallback) {
        var normalized = String(value == null ? '' : value).replace(/[^\d.-]/g, '');
        var parsed = Number(normalized);
        return Number.isFinite(parsed) ? parsed : fallback;
    }

    function getRuntimeSource() {
        if (typeof yumehoPricing === 'object' && yumehoPricing) {
            return yumehoPricing;
        }
        return {};
    }

    function buildOptionCatalog(runtime) {
        var catalog = {};
        Object.keys(DEFAULT_OPTIONS).forEach(function (key) {
            catalog[key] = Object.assign({}, DEFAULT_OPTIONS[key]);
        });

        if (runtime.options && typeof runtime.options === 'object') {
            Object.keys(runtime.options).forEach(function (key) {
                if (!runtime.options[key] || typeof runtime.options[key] !== 'object') {
                    return;
                }
                catalog[key] = Object.assign({}, catalog[key] || {}, runtime.options[key]);
            });
        }

        return catalog;
    }

    function buildSystemCatalog(runtime) {
        var defaultSystemsByCode = {};

        DEFAULT_SYSTEMS.forEach(function (item) {
            defaultSystemsByCode[item.code] = item;
        });

        if (!Array.isArray(runtime.systems) || runtime.systems.length === 0) {
            return DEFAULT_SYSTEMS.slice();
        }

        return runtime.systems
            .filter(function (item) { return item && typeof item === 'object'; })
            .map(function (item) {
                var base = item.code && defaultSystemsByCode[item.code]
                    ? Object.assign({}, defaultSystemsByCode[item.code])
                    : {
                    code: '',
                    displayName: '',
                    shortName: '',
                    spec: '',
                    installType: '',
                    maxRailLength: 0,
                    railLengthOptions: [],
                    unitPrice: 0,
                    railPricePerM: 0,
                    resultName: '',
                    railLabel: ''
                };
                var merged = Object.assign(base, item);
                merged.railLengthOptions = sanitizeRailLengthOptions(
                    Array.isArray(item.railLengthOptions) ? item.railLengthOptions : base.railLengthOptions
                );

                if (merged.installType === 'ceiling' && merged.railLengthOptions.length === 0) {
                    merged.railLengthOptions = buildDefaultCeilingRailLengthOptions();
                }

                if (merged.installType === 'stand' && merged.railLengthOptions.length === 0 && Number(merged.maxRailLength || 0) > 0) {
                    merged.railLengthOptions = [Number(merged.maxRailLength)];
                }

                return merged;
            });
    }

    function buildRuntimeConfig() {
        var runtime = getRuntimeSource();
        var optionCatalog = buildOptionCatalog(runtime);
        var systemCatalog = buildSystemCatalog(runtime);
        var optionPriceMap = {};
        var defaultStandSystem = null;
        var defaultCeilingSystem = null;

        Object.keys(optionCatalog).forEach(function (key) {
            optionPriceMap[key] = toNumber(optionCatalog[key].price, 0);
        });

        systemCatalog.forEach(function (item) {
            if (!defaultStandSystem && item.installType === 'stand') {
                defaultStandSystem = item;
            }
            if (!defaultCeilingSystem && item.installType === 'ceiling') {
                defaultCeilingSystem = item;
            }
        });

        return Object.freeze({
            basePriceMap: Object.freeze({
                ceiling: toNumber(runtime.ceilingPrice, toNumber(defaultCeilingSystem && defaultCeilingSystem.unitPrice, 950000)),
                stand: toNumber(runtime.standPrice, toNumber(defaultStandSystem && defaultStandSystem.unitPrice, 1150000))
            }),
            railPerMeter: toNumber(runtime.railPricePerM, toNumber(defaultStandSystem && defaultStandSystem.railPricePerM, 30000)),
            harnessPerUnit: toNumber(runtime.harnessPrice, 200000),
            optionCatalog: Object.freeze(optionCatalog),
            optionPriceMap: Object.freeze(optionPriceMap),
            systemCatalog: Object.freeze(systemCatalog)
        });
    }

    var PRICE_CONFIG = buildRuntimeConfig();

    function addAlias(map, alias, canonical) {
        if (!alias) {
            return;
        }
        var key = String(alias).trim();
        if (!key) {
            return;
        }
        map[key] = canonical;
        map[key.toLowerCase()] = canonical;
    }

    var OPTION_ALIAS_MAP = {};
    Object.keys(PRICE_CONFIG.optionCatalog).forEach(function (key) {
        var item = PRICE_CONFIG.optionCatalog[key];
        addAlias(OPTION_ALIAS_MAP, key, key);
        addAlias(OPTION_ALIAS_MAP, item.shortLabel, key);
        addAlias(OPTION_ALIAS_MAP, item.label, key);
    });

    addAlias(OPTION_ALIAS_MAP, '計測キット', 'measure');
    addAlias(OPTION_ALIAS_MAP, '歩行データ計測キット', 'measure');
    addAlias(OPTION_ALIAS_MAP, 'JRX', 'jrx');
    addAlias(OPTION_ALIAS_MAP, 'T-Pulling', 'pulling');
    addAlias(OPTION_ALIAS_MAP, 'T-Sling', 'sling');
    addAlias(OPTION_ALIAS_MAP, 'G-Cord', 'gcord');
    addAlias(OPTION_ALIAS_MAP, 'SnG', 'sng');

    function normalizeInstallTypeKey(installType) {
        if (installType === '天井直付型' || installType === 'ceiling') {
            return 'ceiling';
        }

        if (installType === 'スタンド型' || installType === 'stand') {
            return 'stand';
        }

        return String(installType == null ? '' : installType).trim();
    }

    function normalizeOptionKey(value) {
        var raw = String(value == null ? '' : value).trim();
        if (!raw) {
            return '';
        }
        return OPTION_ALIAS_MAP[raw] || OPTION_ALIAS_MAP[raw.toLowerCase()] || '';
    }

    function getRailLengthChoices(installType) {
        var normalizedInstallType = normalizeInstallTypeKey(installType || 'ceiling');

        if (normalizedInstallType === 'ceiling') {
            var ceilingSystem = PRICE_CONFIG.systemCatalog.find(function (item) {
                return item.installType === 'ceiling';
            });
            var ceilingOptions = sanitizeRailLengthOptions(ceilingSystem && ceilingSystem.railLengthOptions);

            if (ceilingOptions.length === 0) {
                ceilingOptions = buildDefaultCeilingRailLengthOptions();
            }

            return ceilingOptions.map(function (value) {
                return {
                    value: value,
                    label: value + 'm',
                    note: value + 'm'
                };
            });
        }

        if (normalizedInstallType === 'stand') {
            return PRICE_CONFIG.systemCatalog
                .filter(function (item) {
                    return item.installType === 'stand' && Number(item.maxRailLength || 0) > 0;
                })
                .sort(function (left, right) {
                    return Number(left.maxRailLength || 0) - Number(right.maxRailLength || 0);
                })
                .map(function (item) {
                    var value = Number(item.maxRailLength || 0);
                    var shortName = item.shortName || item.displayName || '';
                    var displayName = item.displayName || shortName || '';
                    return {
                        value: value,
                        label: shortName ? value + 'm（' + shortName + '）' : value + 'm',
                        note: displayName && item.spec ? displayName + '（' + item.spec + '）' : (displayName || ('総レール長 ' + value + 'm'))
                    };
                });
        }

        return [];
    }

    function normalizeRailLength(value, installType) {
        var parsed = Number(value);
        if (!Number.isFinite(parsed)) {
            throw new Error('レール長が不正です。');
        }

        parsed = Math.round(parsed);

        var choices = getRailLengthChoices(installType);
        if (choices.length > 0) {
            var isAllowed = choices.some(function (choice) {
                return Number(choice.value) === parsed;
            });

            if (!isAllowed) {
                throw new Error('選択肢にないレール長です。');
            }

            return parsed;
        }

        return Math.max(3, Math.min(20, parsed));
    }

    function normalizeHarnessCount(value) {
        var parsed = Number(value);
        if (!Number.isFinite(parsed)) return 0;
        return Math.max(0, Math.min(10, Math.round(parsed)));
    }

    function normalizeOptions(rawOptions) {
        var result = {};

        if (rawOptions && typeof rawOptions === 'object' && !Array.isArray(rawOptions)) {
            Object.keys(rawOptions).forEach(function (key) {
                var normalizedKey = normalizeOptionKey(key);
                if (!normalizedKey || !Object.prototype.hasOwnProperty.call(PRICE_CONFIG.optionPriceMap, normalizedKey)) {
                    return;
                }
                var qty = Math.max(0, Math.min(10, Math.round(Number(rawOptions[key]) || 0)));
                if (qty > 0) {
                    result[normalizedKey] = qty;
                }
            });
            return result;
        }

        var list = Array.isArray(rawOptions) ? rawOptions : [];
        list.forEach(function (optionName) {
            var normalizedKey = normalizeOptionKey(optionName);
            if (!normalizedKey || !Object.prototype.hasOwnProperty.call(PRICE_CONFIG.optionPriceMap, normalizedKey)) {
                return;
            }
            result[normalizedKey] = (result[normalizedKey] || 0) + 1;
        });

        return result;
    }

    function calcOptionsTotal(options) {
        return Object.keys(options).reduce(function (sum, optionKey) {
            return sum + PRICE_CONFIG.optionPriceMap[optionKey] * options[optionKey];
        }, 0);
    }

    function formatJPY(value) {
        return '¥' + Number(value).toLocaleString('ja-JP');
    }

    function getOptionLabel(optionKey) {
        var normalizedKey = normalizeOptionKey(optionKey);
        if (!normalizedKey || !PRICE_CONFIG.optionCatalog[normalizedKey]) {
            return String(optionKey || '');
        }
        return PRICE_CONFIG.optionCatalog[normalizedKey].shortLabel || PRICE_CONFIG.optionCatalog[normalizedKey].label || normalizedKey;
    }

    function pickSystemModel(installType, railLength) {
        var normalizedInstallType = normalizeInstallTypeKey(installType);
        var candidates = PRICE_CONFIG.systemCatalog.filter(function (item) {
            return item.installType === normalizedInstallType;
        });

        if (candidates.length === 0) {
            return null;
        }

        if (normalizedInstallType === 'ceiling') {
            return candidates[0];
        }

        candidates.sort(function (left, right) {
            return Number(left.maxRailLength || 0) - Number(right.maxRailLength || 0);
        });

        for (var i = 0; i < candidates.length; i += 1) {
            if (!candidates[i].maxRailLength || railLength <= Number(candidates[i].maxRailLength)) {
                return candidates[i];
            }
        }

        return candidates[candidates.length - 1];
    }

    function calculateQuote(input) {
        if (!input) {
            throw new Error('見積入力が不足しています。');
        }

        var facilityType = input.facilityType;
        var installType = input.installType;
        var options = normalizeOptions(input.options);
        var harnessCount = normalizeHarnessCount(input.harnessCount);

        if (!facilityType) {
            throw new Error('施設種別を選択してください。');
        }
        if (installType !== '天井直付型' && installType !== 'スタンド型') {
            throw new Error('設置方式が不正です。');
        }

        var railLength = normalizeRailLength(input.railLength, installType);
        var normalizedInstallType = normalizeInstallTypeKey(installType);
        var systemModel = pickSystemModel(installType, railLength);
        var systemBase = systemModel
            ? toNumber(systemModel.unitPrice, normalizedInstallType === 'ceiling'
                ? PRICE_CONFIG.basePriceMap.ceiling
                : PRICE_CONFIG.basePriceMap.stand)
            : (normalizedInstallType === 'ceiling' ? PRICE_CONFIG.basePriceMap.ceiling : PRICE_CONFIG.basePriceMap.stand);
        var railPerMeter = systemModel
            ? toNumber(systemModel.railPricePerM, PRICE_CONFIG.railPerMeter)
            : PRICE_CONFIG.railPerMeter;
        var railTotal = railPerMeter * railLength;
        var optionsTotal = calcOptionsTotal(options);
        var harnessTotal = PRICE_CONFIG.harnessPerUnit * harnessCount;
        var total = systemBase + railTotal + optionsTotal + harnessTotal;

        return {
            facilityType: facilityType,
            installType: installType,
            railLength: railLength,
            options: options,
            harnessCount: harnessCount,
            modelName: systemModel
                ? (systemModel.resultName || systemModel.displayName || '')
                : (installType === '天井直付型' ? '天井直付型 FCW-3000' : 'スタンド型 PGT-9000'),
            railLabel: normalizedInstallType === 'stand'
                ? (systemModel && systemModel.railLabel ? systemModel.railLabel : '総レール長 ' + railLength + 'm')
                : '全長 ' + railLength + 'm（カスタム設計）',
            lineItems: [
                { label: '本体システム', amount: systemBase },
                { label: 'レール構成', amount: railTotal },
                { label: '追加ハーネス (' + harnessCount + '着)', amount: harnessTotal },
                { label: 'オプション合計', amount: optionsTotal }
            ],
            totalExcludingTax: total
        };
    }

    return {
        PRICE_CONFIG: PRICE_CONFIG,
        calculateQuote: calculateQuote,
        formatJPY: formatJPY,
        getOptionLabel: getOptionLabel,
        getRailLengthChoices: getRailLengthChoices
    };
}));
