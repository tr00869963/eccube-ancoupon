<!--{*
 * アフィリナビクーポンプラグイン
 * Copyright (C) 2013 M-soft All Rights Reserved.
 * http://m-soft.jp/
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *}-->

<div id="complete">
    <div class="complete-top"></div>
    <div class="contents">
        <div class="message">
            <a href="plg_AnCoupon_discount_rule_edit.php?discount_rule_id=<!--{$discount_rule->discount_rule_id|h}-->"><!--{$discount_rule->name|h}--></a>の保存が完了致しました。
        </div>
    </div>
    <div class="btn-area-top"></div>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="plg_AnCoupon_discount_rule_list.php"><span class="btn-next">一覧に移動する</span></a></li>
            <li><a class="btn-action" href="plg_AnCoupon_discount_rule_edit.php"><span class="btn-next">新しい割引条件を追加する</span></a></li>
        </ul>
    </div>
    <div class="btn-area-bottom"></div>
</div>
