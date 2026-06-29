@props(['colspan' => 1, 'title' => 'Belum ada data', 'message' => 'Data yang Anda cari belum tersedia.'])

<tr class="ubp-table-empty-row">
    <td colspan="{{ $colspan }}">
        <x-ui.empty-state :title="$title" :message="$message" />
    </td>
</tr>
