<?php

/**
 * includes/queries.php
 * All the SQL behind the Dashboard KPIs and the Charts page lives here in
 * one place. To change what a chart shows, edit the query in this file —
 * the page files just call these functions and hand the result to Chart.js.
 */

/** Top KPI numbers shown as cards on the dashboard. */
function get_kpi_totals(): array
{
	$totals = itr_db()->query(
		"SELECT
				COUNT(*)                                              AS total_applications,
				COALESCE(SUM(rebate_amount), 0)                       AS total_rebate,
				COALESCE(AVG(NULLIF(rebate_amount, 0)), 0)             AS avg_rebate,
				SUM(status IN ('New','Awaiting Agent Link','Processing')) AS processing_count,
				SUM(status = 'Paid')                                   AS paid_count
			FROM applications"
	)->fetch();

	return $totals ?: [
		'total_applications' => 0,
		'total_rebate' => 0,
		'avg_rebate' => 0,
		'processing_count' => 0,
		'paid_count' => 0,
	];
}

/**
 * Rebate value issued per calendar week (based on `submitted_at` date).
 * Change the DATE_FORMAT/GROUP BY here to switch to daily or monthly
 * grouping — the chart code needs no changes.
 */
function get_weekly_rebate_totals(): array
{
	$rows = itr_db()->query(
		"SELECT CONCAT('Wk ', CEIL(DAYOFMONTH(submitted_at) / 7)) AS week_label,
                SUM(rebate_amount) AS total
         FROM applications
         GROUP BY CEIL(DAYOFMONTH(submitted_at) / 7)
         ORDER BY MIN(submitted_at)"
	)->fetchAll();

	$labels = array_column($rows, 'week_label');
	$values = array_map(fn($v) => round((float) $v, 2), array_column($rows, 'total'));
	return ['labels' => $labels, 'values' => $values];
}

/** How many applications fall into each status — feeds the status doughnut. */
function get_status_breakdown(): array
{
	$rows = itr_db()->query(
		"SELECT status, COUNT(*) AS total FROM applications GROUP BY status"
	)->fetchAll();

	return ['labels' => array_column($rows, 'status'), 'values' => array_map('intval', array_column($rows, 'total'))];
}

/**
 * Total rebate value grouped by rebate type — feeds the bar/doughnut charts.
 * `rebate_type` is only set once an admin classifies the application during
 * review, so brand-new submissions (NULL) are grouped under "Unclassified".
 */
function get_rebate_totals_by_type(): array
{
	$rows = itr_db()->query(
		"SELECT COALESCE(rebate_type, 'Unclassified') AS type, SUM(rebate_amount) AS total
         FROM applications GROUP BY COALESCE(rebate_type, 'Unclassified') ORDER BY total DESC"
	)->fetchAll();

	return [
		'labels' => array_column($rows, 'type'),
		'values' => array_map(fn($v) => round((float) $v, 2), array_column($rows, 'total')),
	];
}

/** Applications received vs. rebates paid out per week — line chart on Charts page. */
function get_weekly_applications_vs_rebates(): array
{
	$rows = itr_db()->query(
		"SELECT CONCAT('Wk ', CEIL(DAYOFMONTH(submitted_at) / 7)) AS week_label,
                COUNT(*) AS application_count,
                SUM(rebate_amount) AS total_rebate
         FROM applications
         GROUP BY CEIL(DAYOFMONTH(submitted_at) / 7)
         ORDER BY MIN(submitted_at)"
	)->fetchAll();

	return [
		'labels'       => array_column($rows, 'week_label'),
		'applications' => array_map('intval', array_column($rows, 'application_count')),
		'rebates'      => array_map(fn($v) => round((float) $v / 100, 2), array_column($rows, 'total_rebate')), // scaled to €00s for chart readability
	];
}

/** Applications broken down by county — feeds the geography chart on Charts page. */
function get_applications_by_county(): array
{
	$rows = itr_db()->query(
		"SELECT county, COUNT(*) AS total FROM applications GROUP BY county ORDER BY total DESC LIMIT 8"
	)->fetchAll();

	return ['labels' => array_column($rows, 'county'), 'values' => array_map('intval', array_column($rows, 'total'))];
}
