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
    }
}, {
  tableName: 'inventaris',
  timestamps: false 
});

// Relationships
const Barang = require('./Barang');
Inventaris.belongsTo(Barang, { foreignKey: 'barang_id', as: 'barang' });

module.exports = Inventaris;
