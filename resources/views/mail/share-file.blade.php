<!DOCTYPE html>
<html lang="id">
<head>
    <title>Notifikasi Berbagi File</title>
</head>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #ddd;
    }
</style>
<body>

    <p>Halo {{ $user->name }},</p>

    <p>Anda telah menerima berkas berbagi dari {{ $author->name }}.</p>

    <table>
        <thead>
            <tr>
                <th>Nama File</th>
                <th>Ukuran</th>
                <th>Tanggal Dibagikan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
                <tr>
                    <td>{{ $file->is_folder ? 'Folder' : 'File' }} - {{ $file->name }}</td>
                    <td>{{ $file->get_file_size()}} </td>
                    <td>{{ $file->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Terima kasih telah menggunakan layanan kami.</p>
</body>
</html>
