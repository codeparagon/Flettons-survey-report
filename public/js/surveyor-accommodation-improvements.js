/**
 * Accommodation group summaries, normalized state, and staleness (survey data mock page).
 * Depends on jQuery (loaded before this file).
 */
(function (window, $) {
    'use strict';

    var NS = 'SurveyorAccommodationImprovements';

    function stableStringify(obj) {
        return JSON.stringify(sortKeysRecursive(obj));
    }

    function sortKeysRecursive(value) {
        if (value === null || typeof value !== 'object') {
            return value;
        }
        if (Array.isArray(value)) {
            return value.map(sortKeysRecursive);
        }
        var sorted = {};
        Object.keys(value).sort().forEach(function (k) {
            sorted[k] = sortKeysRecursive(value[k]);
        });
        return sorted;
    }

    function hashInputState(obj) {
        var str = stableStringify(obj);
        var hash = 5381;
        for (var i = 0; i < str.length; i++) {
            hash = ((hash << 5) + hash) + str.charCodeAt(i);
            hash = hash & hash;
        }
        return 'h' + (hash >>> 0).toString(16);
    }

    /**
     * @param {JQuery} $item .survey-data-mock-section-item[data-accommodation-id]
     * @returns {{ custom_name: string, notes: string, components: Array<{component_key: string, component_name: string, material: string, defects: string[]}> }}
     */
    function collectAccommodationFormData($item) {
        var $details = $item.find('.survey-data-mock-section-details');
        var accommodationName = $item.find('.survey-data-mock-section-name').first().text().trim();
        var formData = {
            custom_name: accommodationName,
            notes: $details.find('.survey-data-mock-notes-input').val() || '',
            components: []
        };

        $details.find('.survey-data-mock-carousel-slide').each(function () {
            var $slide = $(this);
            var componentKey = $slide.data('component-key');
            if (!componentKey) {
                return;
            }
            var componentName = $item.find('.survey-data-mock-component-tab[data-component-key="' + componentKey + '"]').text().trim() || componentKey;
            var material = $slide.find('[data-group="material"].active').data('value') || '';
            var defects = $slide.find('[data-group="defects"].active').map(function () {
                return $(this).data('value');
            }).get();
            formData.components.push({
                component_key: componentKey,
                component_name: componentName,
                material: material,
                defects: defects
            });
        });

        return formData;
    }

    function inputHashFromFormData(formData) {
        var forHash = {
            notes: formData.notes || '',
            components: (formData.components || []).map(function (c) {
                return {
                    component_key: c.component_key,
                    material: c.material || '',
                    defects: (c.defects || []).slice().sort()
                };
            })
        };
        return hashInputState(forHash);
    }

    var roomState = {};
    var groupGeneratedHash = {};

    function getRoomId($item) {
        var id = $item.attr('data-accommodation-id');
        return id !== undefined && id !== null ? String(id) : '';
    }

    function ensureRoomState(roomId) {
        if (!roomState[roomId]) {
            roomState[roomId] = {
                inputHash: '',
                lastGeneratedHash: '',
                hasReport: false,
                manualReportEdit: false
            };
        }
        return roomState[roomId];
    }

    function syncRoomStateFromItem($item) {
        var roomId = getRoomId($item);
        if (!roomId) {
            return;
        }
        var st = ensureRoomState(roomId);
        var fd = collectAccommodationFormData($item);
        st.inputHash = inputHashFromFormData(fd);
        st.hasReport = $item.attr('data-has-report') === 'true' || $item.attr('data-saved') === 'true';
        return st;
    }

    function updateRoomStalenessUi($item) {
        var roomId = getRoomId($item);
        if (!roomId) {
            return;
        }
        var st = ensureRoomState(roomId);
        syncRoomStateFromItem($item);
        var $badge = $item.find('.survey-data-mock-accommodation-stale-badge');
        var staleInputs = st.hasReport && st.inputHash !== st.lastGeneratedHash;
        var showStale = staleInputs || st.manualReportEdit;

        if (!$badge.length) {
            return;
        }

        $badge.toggle(showStale);
        $badge.attr('data-stale-reason', st.manualReportEdit && !staleInputs ? 'manual' : (staleInputs ? 'inputs' : ''));
        $badge.text(st.manualReportEdit ? 'Edited / stale' : 'Stale');
    }

    function groupAggregatePayload(typeId) {
        var tid = String(typeId);
        var $rows = $('.survey-data-mock-section-item[data-accommodation-id][data-accommodation-type-id="' + tid + '"]');
        if (!$rows.length) {
            return null;
        }

        var typeName = $rows.first().find('.survey-data-mock-section-name').first().text().trim();
        var componentKeysSeen = {};
        var rooms = [];

        $rows.each(function () {
            var $item = $(this);
            var fd = collectAccommodationFormData($item);
            var rid = getRoomId($item);
            fd.components.forEach(function (c) {
                componentKeysSeen[c.component_key] = c.component_name;
            });
            rooms.push({
                accommodation_id: rid,
                room_label: fd.custom_name || typeName,
                notes: fd.notes || '',
                components_by_key: {}
            });
            var last = rooms[rooms.length - 1];
            fd.components.forEach(function (c) {
                last.components_by_key[c.component_key] = {
                    material: c.material || '',
                    defects: (c.defects || []).slice()
                };
            });
        });

        var keys = Object.keys(componentKeysSeen).sort();
        var out = [];
        keys.forEach(function (componentKey) {
            var component_name = componentKeysSeen[componentKey] || componentKey;
            var roomList = rooms.map(function (r) {
                var comp = r.components_by_key[componentKey] || { material: '', defects: [] };
                return {
                    accommodation_id: r.accommodation_id,
                    room_label: r.room_label,
                    material: comp.material,
                    defects: comp.defects.slice().sort(),
                    notes: r.notes
                };
            });
            out.push({
                accommodation_type_id: parseInt(tid, 10),
                accommodation_type_name: typeName,
                component_key: componentKey,
                component_name: component_name,
                rooms: roomList
            });
        });

        return out;
    }

    function aggregateHashForComponent(payloadEntry) {
        return hashInputState(payloadEntry);
    }

    function updateGroupStalenessUi(typeId, componentKey) {
        var key = String(typeId) + ':' + componentKey;
        var $block = $('.survey-data-mock-accommodation-group-component[data-accommodation-type-id="' + String(typeId) + '"][data-component-key="' + componentKey + '"]');
        if (!$block.length) {
            return;
        }
        var list = groupAggregatePayload(typeId);
        if (!list) {
            return;
        }
        var entry = list.filter(function (e) {
            return e.component_key === componentKey;
        })[0];
        if (!entry) {
            return;
        }
        var aggHash = aggregateHashForComponent(entry);
        var last = groupGeneratedHash[key];
        var $status = $block.find('.survey-data-mock-group-summary-status');
        var $ta = $block.find('.survey-data-mock-group-summary-textarea');
        var hasText = ($ta.val() || '').trim() !== '';

        if (last === undefined && !hasText) {
            $status.removeClass('is-stale is-fresh');
            $status.text('Not generated');
            return;
        }

        var stale = last === undefined || last === '' ? hasText : aggHash !== last;
        $status.toggleClass('is-stale', stale);
        $status.toggleClass('is-fresh', !stale);
        $status.text(stale ? 'Stale' : 'Up to date');
    }

    function refreshGroupStaleForType(typeId) {
        var list = groupAggregatePayload(typeId);
        if (!list) {
            return;
        }
        list.forEach(function (e) {
            updateGroupStalenessUi(typeId, e.component_key);
        });
    }

    /**
     * Mock narrative for one component across all rooms in a type group.
     */
    function generateGroupComponentNarrative(payload) {
        var title = payload.component_name || payload.component_key;
        var lines = [];
        lines.push('**' + title + ' (group summary)**');
        lines.push('');
        lines.push('The following summarises ' + title.toLowerCase() + ' across all ' + (payload.accommodation_type_name || 'accommodation') + ' instances surveyed.');
        lines.push('');

        function hasNoSignificantDefects(defects) {
            if (!defects || !defects.length) {
                return true;
            }
            return defects.every(function (d) {
                return d === 'None' || d === 'No Defects';
            });
        }

        (payload.rooms || []).forEach(function (room, idx) {
            var mat = room.material || 'not specified';
            var defects = room.defects && room.defects.length && !hasNoSignificantDefects(room.defects)
                ? room.defects.filter(function (d) {
                    return d !== 'None' && d !== 'No Defects';
                }).join(', ')
                : 'no significant defects';
            lines.push('*' + (room.room_label || 'Room ' + (idx + 1)) + '*');
            lines.push('- Material: ' + mat);
            if (defects !== 'no significant defects') {
                lines.push('- Defects: ' + defects);
            } else {
                lines.push('- Condition: no significant defects observed');
            }
            if (room.notes) {
                lines.push('- Notes: ' + room.notes);
            }
            lines.push('');
        });

        lines.push('**Overall:** ');
        var anyDefects = (payload.rooms || []).some(function (r) {
            return r.defects && r.defects.length && !hasNoSignificantDefects(r.defects);
        });
        if (anyDefects) {
            lines.push('Variations between rooms are noted above; address defects as appropriate.');
        } else {
            lines.push('Across the group, ' + title.toLowerCase() + ' appears broadly consistent with no significant defects.');
        }

        return lines.join('\n');
    }

    function bootstrapFromDom() {
        $('.survey-data-mock-section-item[data-accommodation-id]').each(function () {
            var $item = $(this);
            var roomId = getRoomId($item);
            if (!roomId) {
                return;
            }
            var st = ensureRoomState(roomId);
            var fd = collectAccommodationFormData($item);
            st.inputHash = inputHashFromFormData(fd);
            st.hasReport = $item.attr('data-has-report') === 'true' || $item.attr('data-saved') === 'true';
            st.manualReportEdit = false;
            if (st.hasReport) {
                st.lastGeneratedHash = st.inputHash;
            } else {
                st.lastGeneratedHash = '';
            }
            updateRoomStalenessUi($item);
        });

        $('.survey-data-mock-accommodation-group-component').each(function () {
            var $block = $(this);
            var tid = $block.data('accommodation-type-id');
            var ck = $block.data('component-key');
            var $ta = $block.find('.survey-data-mock-group-summary-textarea');
            if (($ta.val() || '').trim() !== '') {
                var list = groupAggregatePayload(tid);
                var entry = (list || []).filter(function (e) {
                    return e.component_key === ck;
                })[0];
                if (entry) {
                    groupGeneratedHash[String(tid) + ':' + ck] = aggregateHashForComponent(entry);
                }
            }
            updateGroupStalenessUi(tid, ck);
        });
    }

    function markRoomGenerated($item) {
        var roomId = getRoomId($item);
        if (!roomId) {
            return;
        }
        var st = ensureRoomState(roomId);
        syncRoomStateFromItem($item);
        st.lastGeneratedHash = st.inputHash;
        st.manualReportEdit = false;
        st.hasReport = true;
        updateRoomStalenessUi($item);
        var tid = $item.attr('data-accommodation-type-id');
        if (tid) {
            refreshGroupStaleForType(tid);
        }
    }

    function markManualReportEdit($item) {
        var roomId = getRoomId($item);
        if (!roomId) {
            return;
        }
        var st = ensureRoomState(roomId);
        st.manualReportEdit = true;
        updateRoomStalenessUi($item);
    }

    function registerNewAccommodationRow($item) {
        syncRoomStateFromItem($item);
        updateRoomStalenessUi($item);
        var tid = $item.attr('data-accommodation-type-id');
        if (tid) {
            refreshGroupStaleForType(tid);
        }
    }

    function rescanAllGroups() {
        $('.survey-data-mock-accommodation-group-component').each(function () {
            var $block = $(this);
            updateGroupStalenessUi($block.data('accommodation-type-id'), $block.data('component-key'));
        });
    }

    function recordGroupGeneration(typeId, componentKey) {
        var list = groupAggregatePayload(typeId);
        var entry = (list || []).filter(function (e) {
            return e.component_key === componentKey;
        })[0];
        if (!entry) {
            return;
        }
        groupGeneratedHash[String(typeId) + ':' + componentKey] = aggregateHashForComponent(entry);
        updateGroupStalenessUi(typeId, componentKey);
    }

    function clearGroupGenerationRecord(typeId, componentKey) {
        delete groupGeneratedHash[String(typeId) + ':' + componentKey];
        updateGroupStalenessUi(typeId, componentKey);
    }

    var debounceTimers = {};

    function debounceRoomInput(roomId, fn, ms) {
        if (debounceTimers[roomId]) {
            clearTimeout(debounceTimers[roomId]);
        }
        debounceTimers[roomId] = setTimeout(fn, ms || 150);
    }

    window[NS] = {
        stableStringify: stableStringify,
        hashInputState: hashInputState,
        collectAccommodationFormData: collectAccommodationFormData,
        inputHashFromFormData: inputHashFromFormData,
        aggregateByComponentKey: groupAggregatePayload,
        generateGroupComponentNarrative: generateGroupComponentNarrative,
        updateRoomStalenessUi: updateRoomStalenessUi,
        updateGroupStalenessUi: updateGroupStalenessUi,
        bootstrapFromDom: bootstrapFromDom,
        markRoomGenerated: markRoomGenerated,
        markManualReportEdit: markManualReportEdit,
        registerNewAccommodationRow: registerNewAccommodationRow,
        rescanAllGroups: rescanAllGroups,
        refreshGroupStaleForType: refreshGroupStaleForType,
        recordGroupGeneration: recordGroupGeneration,
        clearGroupGenerationRecord: clearGroupGenerationRecord,
        debounceRoomInput: debounceRoomInput,
        getRoomState: function (roomId) {
            return roomState[String(roomId)];
        },
        _groupGeneratedHash: groupGeneratedHash
    };
})(window, jQuery);
