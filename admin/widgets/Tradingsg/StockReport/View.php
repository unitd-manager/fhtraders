<?
class CPL_Admin_Widgets_Tradingsg_StockReport_View extends CP_Admin_Widgets_Tradingsg_StockReport_View
{
    //==================================================================//
    function getWidget() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');


        $text = "
        <h2>Stock Report</h2>
		<div class = 'tableOuter scroll-pane'>
		<table class='thinlist'>
			<thead>
				<tr>
                    <th>Product Name</th>
                    <th>Item Code</th>
                    <th>Model</th>
                    <th>Carton Number</th>
                    <th>Total Stock</th>
                    <th class='txtRight'>Cost Price/Qty</th>
                    <th class='txtRight'>Total Cost</th>
                </tr>
			</thead>
			<tbody>
				{$this->getRowsHTML()}
			</tbody>
		</table>
		</div>
        ";
        return $text;
    }

    function getRowsHTML() {
        $fn 	= Zend_Registry::get('fn');
        $db     = Zend_Registry::get('db');
        $cpCfg 	= Zend_Registry::get('cpCfg');

        $rows = '';
		$siteTitle = '' ;
        $count = 1 ;
        $linkToStock = '' ;
        $sum_purchase_cp_per_qty = '';

        if($cpCfg['cp.excludeStock'] == 1){
            $linkToStock = "AND o.link_stock = 1";
        }

        foreach($this->model->dataArray as $row){

            $StockSql = "
            SELECT
                (SELECT SUM(qty) FROM po_product
                WHERE product_id = {$row['product_id']}) as product_qty_purchased
                ,(SELECT SUM(price) FROM po_product
                WHERE product_id = {$row['product_id']}) as purchase_cp_per_qty
                ,(SELECT SUM(invItem.qty) FROM invoice_item invItem
                LEFT JOIN (invoice inv) ON (inv.invoice_id = invItem.invoice_id AND inv.status != 'Cancelled' )
                LEFT JOIN (`order` o) ON (o.order_id = inv.order_id)
                WHERE record_id = {$row['product_id']}
                AND o.order_status = 'Paid'
                  {$linkToStock}
                ) as product_qty_sold_from_quote
            ";

            $resultStockSql = $db->sql_query($StockSql);
            $rowStockSql    = $db->sql_fetchrow($resultStockSql);

            $stock = $rowStockSql['product_qty_purchased']- $rowStockSql['product_qty_sold_from_quote'];
            $sum_purchase_cp_per_qty = $stock * $rowStockSql['purchase_cp_per_qty'];


            $rowStockSql['purchase_cp_per_qty'] = number_format($rowStockSql['purchase_cp_per_qty']);

            if($sum_purchase_cp_per_qty){
                $sum_purchase_cp_per_qty = number_format($sum_purchase_cp_per_qty);
            }

		    $rows .= "
            <tr>
                <td>{$row['product_title']}</td>
                <td>{$row['item_code']}</td>
                <td>{$row['model']}</td>
                <td>{$row['carton_no']}</td>
                <td>{$stock}</td>
                <td class='txtRight'>{$rowStockSql['purchase_cp_per_qty']}</td>
                <td class='txtRight'>{$sum_purchase_cp_per_qty}</td>
            </tr>
			";
        }

        $text = "
        {$rows}
        ";

        return $text;
    }

}