const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const Inventaris = sequelize.define('Inventaris', {
    kode_inventaris: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    kondisi: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    tanggal_masuk: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    qr_code: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    foto_barang: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    status_barang: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    status_inventaris: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    barang_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    },
    ruangan_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    },
    draft_pengadaan_detail_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    kondisi_pending: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    foto_pending: {
      type: DataTypes.STRING,
      allowNull: true
    },
    status_verifikasi: {
      type: DataTypes.STRING,
      allowNull: true,
      defaultValue: 'terverifikasi'
    }
}, {
  tableName: 'inventaris',
  timestamps: false 
});

// Relationships
const Barang = require('./Barang');
const Ruangan = require('./Ruangan');
Inventaris.belongsTo(Barang, { foreignKey: 'barang_id', as: 'barang' });
Inventaris.belongsTo(Ruangan, { foreignKey: 'ruangan_id', as: 'ruangan' });

module.exports = Inventaris;
