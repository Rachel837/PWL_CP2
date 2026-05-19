const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const Maintenance = sequelize.define('Maintenance', {
    tanggal_maintenance: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    kondisi_sebelum: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    kondisi_sesudah: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    tindakan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    catatan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    inventaris_id: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    users_id: {
      type: DataTypes.STRING, 
      allowNull: true
    }
}, {
  tableName: 'maintenance',
  timestamps: false 
});

module.exports = Maintenance;
