
import { useEffect, useState } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";

const api = window.wpcloudStationApi;
apiFetch.use( apiFetch.createNonceMiddleware( api.nonce ) );

function buildUrl(metric, options) {
	const queryParams = new URLSearchParams( Object.fromEntries(
    Object.entries( options ).filter(([key, value]) => value)
  ));

	return `${window.location.origin}${api.root}/metrics/${metric}` + '?' + queryParams.toString();
}

export default function Graph({ site, metric, type, title, showLegend, interval }) {
	const {start, end} = interval || {};
	const [data, setData] = useState([]);
	const [dataMap, setDataMap] = useState({});
	const [dataMeta, setDataMeta] = useState({});

	useEffect(() => {

		async function fetchData() {
			const url = buildUrl(metric, { site, start, end });
			const result = await apiFetch({ url } );

			setData(result.data);
			setDataMap(result.dataMap);
			setDataMeta(result.dataMeta);
		}

		fetchData();
	}, [ site, metric, start, end ] );


	return (
		<div className="wpcloud-graph">
			<h3>Graph</h3>
		</div>
	);
}
