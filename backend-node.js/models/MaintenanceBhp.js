const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const MaintenanceBhp = sequelize.define('MaintenanceBhp', {
    bhp_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    },
    jumlah_digunakan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    maintenance_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    },
    barang_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    }
}, {
  tableName: 'maintenance_bhp',
  timestamps: false 
});

// Relationships
const Maintenance = require('./Maintenance');
const StokBhp = require('./StokBhp');
const Barang = require('./Barang');
MaintenanceBhp.belongsTo(Maintenance, { foreignKey: 'maintenance_id', as: 'maintenance' });
MaintenanceBhp.belongsTo(StokBhp, { foreignKey: 'bhp_id', as: 'stok_bhp' });
MaintenanceBhp.belongsTo(Barang, { foreignKey: 'barang_id', as: 'barang' });

module.exports = MaintenanceBhp;
