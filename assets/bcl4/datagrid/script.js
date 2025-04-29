/*
 * This file is part of the Osynapsy package.
 *
 * (c) Pietro Celeste <p.celeste@osynapsy.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

const BclDataGrid = 
{
    init: () => 
    {
        document.querySelectorAll('.bcl-datagrid').forEach(datagrid => {
            document.addEventListener('click', function(event) {
                const datagrid = event.target.closest('.bcl-datagrid');
                if (!datagrid) {
                    return;
                }
                if (BclDataGrid.handleRowClick(event.target, datagrid)) return;
                if (BclDataGrid.handleOrderByClick(event.target, datagrid)) return;
                if (BclDataGrid.handleCheckAllClick(event.target, datagrid)) return;                
            });
        });
    },
    handleRowClick : (target, datagrid) => 
    {
        const row = target.closest('.row');
        if (!row || !datagrid.contains(row) || !row.dataset.urlDetail) {
            return false;
        }
        Osynapsy.History.save();
        window.location = row.dataset.urlDetail;
        return true;
    },
    handleOrderByClick : (target, datagrid) => 
    {
        const thOrderBy = target.closest('.bcl-datagrid-th-order-by');
        if (!thOrderBy || !datagrid.contains(thOrderBy) || !thOrderBy.dataset.idx) {
            return false;
        }
        const orderByField = datagrid.querySelector('.BclPaginationOrderBy');
        const currentPageField = datagrid.querySelector('.BclPaginationCurrentPage');
        if (!orderByField || !currentPageField) {
            return false;
        }
        const gridId = datagrid.id;
        const idx = thOrderBy.dataset.idx;
        let orderBy = orderByField.value;
        if (orderBy.includes(`[${idx}]`)) {
            orderBy = orderBy.replace(`[${idx}]`, `[${idx} DESC]`);
        } else if (orderBy.includes(`[${idx} DESC]`)) {
            orderBy = orderBy.replace(`[${idx} DESC]`, '');
        } else {
            orderBy += `[${idx}]`;
        }
        orderByField.value = orderBy;
        currentPageField.value = 1;
        Osynapsy.refreshComponents([gridId]);
        return true;
    },
    handleCheckAllClick : (target, datagrid) =>
    {
        const checkAllCmd = target.closest('.bcl-datagrid-th-check-all');
        if (!checkAllCmd || !datagrid.contains(checkAllCmd)) {
            return false;
        }
        const checkboxes = datagrid.querySelectorAll('.grid-check');
        const total = checkboxes.length;
        const checked = Array.from(checkboxes).filter(chk => chk.checked).length;
        const newState = total > checked;
        checkboxes.forEach(chk => { chk.checked = newState; });
        return true;
    }
};

if (window.Osynapsy) {
    Osynapsy.plugin.register('BclDataGrid', () => BclDataGrid.init());
}
