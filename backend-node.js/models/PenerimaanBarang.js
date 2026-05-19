const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const PenerimaanBarang = sequelize.define('PenerimaanBarang', {
    detail_pengadaan_id: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    tanggal_terima: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    jumlah_diterima: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    catatan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    users_id: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    draft_pengadaan_detail_id: {
      type: DataTypes.STRING, 
      allowNull: true
    }
}, {
  tableName: 'penerimaan_barang',
  timestamps: false 
});

module.exports = PenerimaanBarang;
