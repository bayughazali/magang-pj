    @extends('layouts.app')

    @section('content')
    import React, { useState } from 'react';
import { Shield, Eye, EyeOff, Edit, Trash2, Mail, User, CheckCircle, XCircle } from 'lucide-react';

export default function FeatureFlagsDesign() {
  const [activeRole, setActiveRole] = useState('user');
  const [activeTab, setActiveTab] = useState('overview');

  const features = {
    viewAdminData: {
      name: 'View Admin Data',
      description: 'Kemampuan melihat data lengkap admin (termasuk email)',
      admin: true,
      user: false,
      icon: Eye
    },
    editAdminData: {
      name: 'Edit Admin Data',
      description: 'Kemampuan mengedit data admin',
      admin: true,
      user: false,
      icon: Edit
    },
    deleteAdminData: {
      name: 'Delete Admin Data',
      description: 'Kemampuan menghapus data admin',
      admin: true,
      user: false,
      icon: Trash2
    },
    viewAdminEmail: {
      name: 'View Admin Email',
      description: 'Kemampuan melihat email admin di tabel',
      admin: true,
      user: false,
      icon: Mail
    },
    createUser: {
      name: 'Create User',
      description: 'Kemampuan menambah user baru',
      admin: true,
      user: false,
      icon: User
    }
  };

  const mockUsers = [
    { id: 1, name: 'choir anam', email: 'choir@gmail.com', role: 'admin', photo: null },
    { id: 2, name: 'Adistya', email: 'adis@gmail.com', role: 'user', photo: '👤' },
    { id: 3, name: 'Ryan Maulana', email: 'ryan05@gmail.com', role: 'user', photo: '👤' }
  ];

  const getFilteredUsers = () => {
    if (activeRole === 'admin') {
      return mockUsers;
    }
    return mockUsers.filter(u => u.role !== 'admin');
  };

  const canSeeEmail = (userRole) => {
    if (activeRole === 'admin') return true;
    return userRole !== 'admin';
  };

  const canEdit = (userRole) => {
    if (activeRole !== 'admin') return false;
    return true;
  };

  const codeChanges = [
    {
      file: 'users/index.blade.php',
      description: 'Update view untuk filter tampilan admin',
      before: `<td>{{ $user->email }}</td>`,
      after: `<td>
  @if(auth()->user()->role === 'admin' || $user->role !== 'admin')
    {{ $user->email }}
  @else
    <span class="text-muted">***hidden***</span>
  @endif
</td>`
    },
    {
      file: 'UserController.php',
      description: 'Filter query sudah benar, tambahkan middleware',
      before: `public function index()
{
    $users = User::where('role', 'user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);`,
      after: `public function index()
{
    // Admin bisa lihat semua, user biasa hanya lihat sesama user
    if (auth()->user()->role === 'admin') {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
    } else {
        $users = User::where('role', 'user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
    }

    return view('users.index', compact('users'));
}`
    }
  ];

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-8">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="bg-white rounded-2xl shadow-xl p-8 mb-8">
          <div className="flex items-center gap-4 mb-6">
            <div className="bg-blue-500 p-3 rounded-xl">
              <Shield className="w-8 h-8 text-white" />
            </div>
            <div>
              <h1 className="text-3xl font-bold text-slate-800">Feature Flags: Role-Based Access Control</h1>
              <p className="text-slate-600 mt-1">User Management - Visibility & Permissions</p>
            </div>
          </div>

          {/* Role Switcher */}
          <div className="flex gap-4 bg-slate-100 p-2 rounded-xl inline-flex">
            <button
              onClick={() => setActiveRole('admin')}
              className={`px-6 py-3 rounded-lg font-semibold transition-all ${
                activeRole === 'admin'
                  ? 'bg-red-500 text-white shadow-lg'
                  : 'bg-white text-slate-600 hover:bg-slate-50'
              }`}
            >
              👑 Admin View
            </button>
            <button
              onClick={() => setActiveRole('user')}
              className={`px-6 py-3 rounded-lg font-semibold transition-all ${
                activeRole === 'user'
                  ? 'bg-blue-500 text-white shadow-lg'
                  : 'bg-white text-slate-600 hover:bg-slate-50'
              }`}
            >
              👤 User View
            </button>
          </div>
        </div>

        {/* Tabs */}
        <div className="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
          <div className="flex border-b">
            <button
              onClick={() => setActiveTab('overview')}
              className={`px-6 py-4 font-semibold transition-colors ${
                activeTab === 'overview'
                  ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600'
                  : 'text-slate-600 hover:bg-slate-50'
              }`}
            >
              📊 Feature Matrix
            </button>
            <button
              onClick={() => setActiveTab('preview')}
              className={`px-6 py-4 font-semibold transition-colors ${
                activeTab === 'preview'
                  ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600'
                  : 'text-slate-600 hover:bg-slate-50'
              }`}
            >
              👁️ Live Preview
            </button>
            <button
              onClick={() => setActiveTab('code')}
              className={`px-6 py-4 font-semibold transition-colors ${
                activeTab === 'code'
                  ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600'
                  : 'text-slate-600 hover:bg-slate-50'
              }`}
            >
              💻 Code Changes
            </button>
          </div>

          <div className="p-8">
            {/* Feature Matrix Tab */}
            {activeTab === 'overview' && (
              <div>
                <h2 className="text-2xl font-bold text-slate-800 mb-6">Feature Access Matrix</h2>
                <div className="space-y-4">
                  {Object.entries(features).map(([key, feature]) => {
                    const Icon = feature.icon;
                    return (
                      <div key={key} className="bg-slate-50 rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div className="flex items-start justify-between">
                          <div className="flex items-start gap-4 flex-1">
                            <div className="bg-white p-3 rounded-lg">
                              <Icon className="w-6 h-6 text-slate-600" />
                            </div>
                            <div className="flex-1">
                              <h3 className="text-lg font-semibold text-slate-800">{feature.name}</h3>
                              <p className="text-slate-600 mt-1">{feature.description}</p>
                            </div>
                          </div>
                          <div className="flex gap-8 ml-8">
                            <div className="text-center">
                              <div className="text-xs font-semibold text-slate-500 mb-2">ADMIN</div>
                              {feature.admin ? (
                                <CheckCircle className="w-6 h-6 text-green-500 mx-auto" />
                              ) : (
                                <XCircle className="w-6 h-6 text-red-400 mx-auto" />
                              )}
                            </div>
                            <div className="text-center">
                              <div className="text-xs font-semibold text-slate-500 mb-2">USER</div>
                              {feature.user ? (
                                <CheckCircle className="w-6 h-6 text-green-500 mx-auto" />
                              ) : (
                                <XCircle className="w-6 h-6 text-red-400 mx-auto" />
                              )}
                            </div>
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>
            )}

            {/* Live Preview Tab */}
            {activeTab === 'preview' && (
              <div>
                <div className="mb-6">
                  <h2 className="text-2xl font-bold text-slate-800 mb-2">Live Preview</h2>
                  <p className="text-slate-600">Berikut tampilan tabel berdasarkan role: <span className="font-semibold text-blue-600">{activeRole.toUpperCase()}</span></p>
                </div>

                <div className="bg-slate-50 rounded-xl p-6">
                  <div className="overflow-x-auto">
                    <table className="w-full">
                      <thead>
                        <tr className="bg-slate-800 text-white">
                          <th className="px-4 py-3 text-left rounded-tl-lg">#</th>
                          <th className="px-4 py-3 text-left">Foto</th>
                          <th className="px-4 py-3 text-left">Nama</th>
                          <th className="px-4 py-3 text-left">Email</th>
                          <th className="px-4 py-3 text-left">Role</th>
                          {activeRole === 'admin' && (
                            <th className="px-4 py-3 text-center rounded-tr-lg">Aksi</th>
                          )}
                        </tr>
                      </thead>
                      <tbody className="bg-white">
                        {getFilteredUsers().map((user, idx) => (
                          <tr key={user.id} className="border-b border-slate-200 hover:bg-slate-50 transition-colors">
                            <td className="px-4 py-4">{idx + 1}</td>
                            <td className="px-4 py-4">
                              <div className="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center text-2xl">
                                {user.photo || '👤'}
                              </div>
                            </td>
                            <td className="px-4 py-4 font-semibold">{user.name}</td>
                            <td className="px-4 py-4">
                              {canSeeEmail(user.role) ? (
                                user.email
                              ) : (
                                <span className="text-slate-400 italic">***hidden***</span>
                              )}
                            </td>
                            <td className="px-4 py-4">
                              <span className={`px-3 py-1 rounded-full text-xs font-semibold uppercase ${
                                user.role === 'admin' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'
                              }`}>
                                {user.role}
                              </span>
                            </td>
                            {activeRole === 'admin' && (
                              <td className="px-4 py-4">
                                <div className="flex gap-2 justify-center">
                                  <button
                                    disabled={!canEdit(user.role)}
                                    className={`px-3 py-1 rounded-lg text-sm font-semibold flex items-center gap-1 ${
                                      canEdit(user.role)
                                        ? 'bg-yellow-500 text-white hover:bg-yellow-600'
                                        : 'bg-slate-200 text-slate-400 cursor-not-allowed'
                                    }`}
                                  >
                                    <Edit className="w-4 h-4" /> Edit
                                  </button>
                                  <button
                                    disabled={!canEdit(user.role) || user.id === 1}
                                    className={`px-3 py-1 rounded-lg text-sm font-semibold flex items-center gap-1 ${
                                      canEdit(user.role) && user.id !== 1
                                        ? 'bg-red-500 text-white hover:bg-red-600'
                                        : 'bg-slate-200 text-slate-400 cursor-not-allowed'
                                    }`}
                                  >
                                    <Trash2 className="w-4 h-4" /> Hapus
                                  </button>
                                </div>
                              </td>
                            )}
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>

                {/* Legend */}
                <div className="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                  <h3 className="font-semibold text-blue-900 mb-2">📌 Perubahan yang terlihat:</h3>
                  <ul className="space-y-2 text-sm text-blue-800">
                    {activeRole === 'user' ? (
                      <>
                        <li>✓ Data admin (choir anam) tidak muncul di tabel</li>
                        <li>✓ Email admin tidak terlihat</li>
                        <li>✓ Kolom "Aksi" tidak ditampilkan</li>
                        <li>✓ Hanya bisa melihat data sesama user</li>
                      </>
                    ) : (
                      <>
                        <li>✓ Semua data terlihat (termasuk admin)</li>
                        <li>✓ Email semua user terlihat</li>
                        <li>✓ Tombol Edit & Hapus tersedia</li>
                        <li>✓ Full control atas user management</li>
                      </>
                    )}
                  </ul>
                </div>
              </div>
            )}

            {/* Code Changes Tab */}
            {activeTab === 'code' && (
              <div>
                <h2 className="text-2xl font-bold text-slate-800 mb-6">Perubahan Kode yang Diperlukan</h2>
                <div className="space-y-6">
                  {codeChanges.map((change, idx) => (
                    <div key={idx} className="bg-slate-50 rounded-xl p-6">
                      <div className="flex items-center gap-3 mb-4">
                        <div className="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                          {idx + 1}
                        </div>
                        <div>
                          <h3 className="font-semibold text-slate-800">{change.file}</h3>
                          <p className="text-sm text-slate-600">{change.description}</p>
                        </div>
                      </div>

                      <div className="space-y-4">
                        <div>
                          <div className="text-xs font-semibold text-red-600 mb-2">❌ BEFORE:</div>
                          <pre className="bg-red-50 border border-red-200 rounded-lg p-4 text-sm overflow-x-auto">
                            <code>{change.before}</code>
                          </pre>
                        </div>
                        <div>
                          <div className="text-xs font-semibold text-green-600 mb-2">✅ AFTER:</div>
                          <pre className="bg-green-50 border border-green-200 rounded-lg p-4 text-sm overflow-x-auto">
                            <code>{change.after}</code>
                          </pre>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>

                {/* Implementation Notes */}
                <div className="mt-8 bg-amber-50 border border-amber-200 rounded-xl p-6">
                  <h3 className="font-semibold text-amber-900 mb-3">⚠️ Catatan Implementasi</h3>
                  <ul className="space-y-2 text-sm text-amber-800">
                    <li>• Pastikan middleware auth sudah terpasang di route</li>
                    <li>• Test dengan kedua role (admin & user) setelah implementasi</li>
                    <li>• Validasi juga di sisi controller untuk keamanan ganda</li>
                    <li>• Pertimbangkan menggunakan Policy Laravel untuk authorization yang lebih scalable</li>
                  </ul>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
