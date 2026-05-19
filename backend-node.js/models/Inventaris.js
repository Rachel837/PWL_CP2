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
      type: DataTypes.STRING, 
      allowNull: true
    },
    ruangan_id: {
      type: DataTypes.STRING, 
      allowNull: true
    }
}, {
  tableName: 'inventaris',
  timestamps: false 
});

module.exports = Inventaris;
