<!--{*
 * アフィリナビクーポンプラグイン
 * Copyright (C) 2014 M-soft All Rights Reserved.
 * http://m-soft.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->

<div id="undercolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    
    <div id="undercolumn_coupon">
        <p>クーポンの使用を止めると現在購入中の商品に対して割引が受けられなくなります。割引を再度受けたい時はクーポンコードを入力し直して下さい。</p>

        <form name="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="execute" />
            <input type="hidden" name="context" value="<!--{$context|h}-->" />
            
            <div class="btn_area">
                <ul>
                    <li>
                        <input type="submit" name="submit" value="クーポンの使用を止める" />
                    </li>
                </ul>
            </div>
        </form>
    </div>
</div>
