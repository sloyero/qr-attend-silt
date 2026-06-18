-- SQL Schema untuk QR-Attend (PostgreSQL / Supabase)
-- Dibuat berdasarkan file-file migrasi Laravel

-- Hapus tabel jika sudah ada (untuk reset)
DROP TABLE IF EXISTS kehadirans CASCADE;
DROP TABLE IF EXISTS sesi_presensis CASCADE;
DROP TABLE IF EXISTS mata_kuliahs CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS personal_access_tokens CASCADE;

-- 1. TABEL: users
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'mahasiswa' NOT NULL, -- admin, dosen, mahasiswa
    nim VARCHAR(50) NULL,
    photo VARCHAR(255) NULL,
    nidn VARCHAR(50) NULL,
    matkul VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2. TABEL: password_reset_tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

-- 3. TABEL: sessions
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INT NOT NULL
);

-- 4. TABEL: mata_kuliahs
CREATE TABLE mata_kuliahs (
    id BIGSERIAL PRIMARY KEY,
    nama_matkul VARCHAR(255) NOT NULL,
    dosen_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 5. TABEL: sesi_presensis
CREATE TABLE sesi_presensis (
    id BIGSERIAL PRIMARY KEY,
    dosen_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    mata_kuliah_id BIGINT NOT NULL REFERENCES mata_kuliahs(id) ON DELETE CASCADE,
    token VARCHAR(255) UNIQUE NOT NULL,
    durasi_menit INT DEFAULT 15 NOT NULL,
    started_at TIMESTAMP NOT NULL,
    expired_at TIMESTAMP NOT NULL,
    is_active BOOLEAN DEFAULT TRUE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 6. TABEL: kehadirans
CREATE TABLE kehadirans (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    mata_kuliah_id BIGINT NOT NULL REFERENCES mata_kuliahs(id) ON DELETE CASCADE,
    sesi_presensi_id BIGINT NULL REFERENCES sesi_presensis(id) ON DELETE CASCADE,
    waktu_scan TIMESTAMP NOT NULL,
    status VARCHAR(50) DEFAULT 'hadir' NOT NULL, -- hadir, telat, izin, alpha
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 7. TABEL: personal_access_tokens
CREATE TABLE personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- ============================================================================
-- SEED DATA (DATA AWAL)
-- Password untuk semua akun default adalah: password
-- ============================================================================

-- Seed: Users
-- 1. Admin
INSERT INTO users (id, name, email, password, role, created_at, updated_at)
VALUES (1, 'Admin', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW());

-- 2. Dosen (Pak Fatan)
INSERT INTO users (id, name, email, password, role, nidn, matkul, created_at, updated_at)
VALUES (2, 'Pak Fatan', 'dosen@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen', '12345678', 'Pemrograman Web, Basis Data, Struktur Data', NOW(), NOW());

-- 3. Mahasiswa
INSERT INTO users (id, name, email, password, role, nim, created_at, updated_at)
VALUES 
(3, 'Wildan', 'mahasiswa@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', '231110027', NOW(), NOW()),
(4, 'Nasrul Fawzi', 'mahasiswa2@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', '231110028', NOW(), NOW()),
(5, 'Fikri Karunia', 'mahasiswa3@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', '231110029', NOW(), NOW()),
(6, 'Sayyid Ashhabussnan', 'mahasiswa4@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', '231110030', NOW(), NOW());

-- Seed: Mata Kuliah
INSERT INTO mata_kuliahs (id, nama_matkul, dosen_id, created_at, updated_at)
VALUES 
(1, 'Pemrograman Web', 2, NOW(), NOW()),
(2, 'Basis Data', 2, NOW(), NOW()),
(3, 'Struktur Data', 2, NOW(), NOW());

-- Sesuaikan sequence id di PostgreSQL agar serial auto-increment berjalan lancar setelah disisipkan data manual
SELECT setval('users_id_seq', (SELECT MAX(id) FROM users));
SELECT setval('mata_kuliahs_id_seq', (SELECT MAX(id) FROM mata_kuliahs));
