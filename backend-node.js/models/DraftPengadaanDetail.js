const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const DraftPengadaanDetail = sequelize.define('DraftPengadaanDetail', {
    jumlah: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    harga_estimasi: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    link_pembelian: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    status_approval: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    catatan_kaprodi: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    draft_pengadaan_id: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    barang_id: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    inventaris_id: {
      type: DataTypes.STRING, 
      allowNull: true
    }
}, {
  tableName: 'draft_pengadaan_detail',
  timestamps: false 
});

module.exports = DraftPengadaanDetail;
